# email-service
Simple service to queue and send emails


## Requirements
1. [Docker](https://www.docker.com/get-docker)
2. [Make](https://www.gnu.org/software/make/) 

## Initialize app
1. `cp local.env.dist local.env` and populate required values
2. `make`
3. Upon successful initialization, the API will be available locally at `http://localhost:8080/email`

## Configuration
By default, configuration is read from environment variables. These are documented
in the `local.env.dist` file. Optionally, you can define configuration in AWS AppConfig.
To do this, set the following environment variables to point to the configuration in
AWS:

* `AWS_REGION` - the AWS region in use
* `APP_ID` - the application ID or name
* `CONFIG_ID` - the configuration profile ID or name
* `ENV_ID` - the environment ID or name

In addition, the AWS API requires authentication. It is best to use an access role
such as an [ECS Task Role](https://docs.aws.amazon.com/AmazonECS/latest/developerguide/task-iam-roles.html).
If that is not an option, you can specify an access token using the `AWS_ACCESS_KEY_ID` and
`AWS_SECRET_ACCESS_KEY` variables.

The content of the configuration profile takes the form of a typical .env file, using
`#` for comments and `=` for variable assignment. Any variables read from AppConfig
will overwrite variables set in the execution environment.

## API
See [api.raml](api.raml) for API docs.

## Branding
HTML emails can be branded using the following environment variables:

### `EMAIL_BRAND_COLOR`
This can be any [CSS color](https://developer.mozilla.org/en-US/docs/Web/CSS/color_value), e.g., `EMAIL_BRAND_COLOR="rgb(0, 93, 154)"`

### `EMAIL_BRAND_LOGO`
This is the fully, qualified URL to an image, e.g., `EMAIL_BRAND_LOGO="https://static.gtis.guru/idp-logo/sil-logo.png"`. 

### Local database
A UI into the database runs automatically when app is running, it can be accessed at [localhost:8001](http://localhost:8001)

### Testing
`make test` will run all tests.

#### Unit
`make testunit` will just run the unit tests.

#### API
`make testapi` will just run the API tests.

#### Emails
To send emails, simply create the request and have the local mailer run:
1.  `POST http://localhost:8080/email` with the required fields.
2.  `make cron` will run the send email process.

 
