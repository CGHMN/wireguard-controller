#!/usr/bin/env bash
set -euo pipefail
cd -- "$(dirname "$0")/.." >/dev/null 2>&1

usage() {
	cat <<-EOF
		$(basename "$0") [-f] [-u]

		Initializes this project.

		Optional arguments:
		  -f	Force re-initialization, even if the environment is already set up
		  -u	Update frontend and backend dependencies only, not overwriting anything else
	EOF
}

while getopts ':fuh' opt; do
	case "${opt}" in
		f) force_reinit=y ;;
		u) update_only=y ;;
		h)
			echo "$(basename "${0}") [-f]"
			exit 0
			;;
		*)
			echo "Invalid argument: -${opt}" >&2
			usage >&2
			exit 1
	esac
done
shift $((OPTIND-1))

repo_name="$(basename "$(realpath "${PWD}/..")")"

if [ "${update_only:-}" = 'y' ]; then
	echo " >> Updating app repository ${repo_name}"
else
	echo " >> Initializing app repository ${repo_name}"
fi

if [ -d 'vendor' ] && [ -e 'lib/.git' ] && [ "${update_only:-}" != 'y' ]; then
	if [ "${force_reinit:-n}" != y ]; then
		echo ' >> This repository seems to have been initialized already.' >&2
		echo ' >> Refusing to re-initialize. Run this script again with the -f flag to force a re-initialization.' >&2
		echo ' >> Warning: This might override existing data!' >&2
		exit 1
	else
		echo " >> Warning: Forcing re-initialization of repository!"
	fi
fi

# ensure all required programs are installed
for p in php composer uuid; do
	if ! which "${p}" >/dev/null; then
		echo " >> Error: Unable to find program ${p} in your PATH!" >&2
		echo ' >> Make sure all the dependencies mentioned in the README.md are met before calling this script.' >&2
		exit 1
	fi
done

# ensure required php modules are installed
for m in \
	ctype curl dom fileinfo filter hash mbstring openssl pcre PDO sqlite3 \
	session redis tokenizer xml \
	sodium gd xmlwriter SimpleXML
do
	if ! grep -qE "^${m}$" <(php -m); then
		echo " >> Error: This project requires the PHP module ${m} to be installed and loaded!" >&2
		exit 1
	fi
done

if [ -f .gitmodules ] && [ "${update_only}" != 'y' ]; then
	echo ' >> Installing Git submodules'
	git submodule update --init --force --remote
fi

echo ' >> Installing Composer packages'
composer install

if [ ! -e '.env' ] && [ -f '.env.example' ] && [ "${update_only:-}" != 'y' ]; then
	echo ' >> Creating .env from example'
	cp '.env.example' '.env'

	read -n1 -r -p ' >> Change the environment variables now? [y|N] '
	echo
	if [ "${REPLY,,}" = y ]; then
		"${EDITOR:-nano}" '.env'
	fi
fi

if ! grep -qE '^APP_KEY=base64.+' .env; then
	echo ' >> Generating application token'
	php artisan key:generate
fi

# if ! grep -qE '^JWT_SECRET=.+' .env; then
# 	echo ' >> Generating JWT token'
# 	php artisan jwt:secret
# fi

if ! grep -qE '^API_KEY=.+' .env; then
	echo ' >> Generating API key'
	sed -i "s/^API_KEY=/API_KEY=$(uuid)/" .env
fi

echo ' >> All done!'
echo ' >> Now you can run "./bin/dev-server.sh" to start the development server,'
echo ' >> run "./bin/docker-artisan.sh migrate:fresh --seed" to seed the database and'
echo ' >> navigate your browser to http://localhost:8000 to see the new app.'
echo ' >> In addition, the database development GUI lives at http://localhost:8080'
echo ' >> and the mailcatcher instance to debug sent mails at http://localhost:8082'
echo ' >>'
echo ' >> Have a nice day! :)'