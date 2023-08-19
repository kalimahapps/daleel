# Development
- Clone the repo locally
- Run `pnpm install` or `npm install`
- Run `Composer install`

## Running the app
- Run `pnpm tw:watch` to compile the tailwind css
  - You can use `pnpm tw:build` to build the tailwind css for production
- Run `./bin/daleel build --show-errors` to build the app
- Run `./bin/daleel serve` to see the app in action (http://localhost:8000)

## Running the tests
- Run `./bin/vendor/pest` to run the tests
- Run `./bin/vendor/pest --coverage` to run the tests with coverage