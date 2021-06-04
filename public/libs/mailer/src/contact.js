import {FormProcess, CSRFHash} from './mailer.js'

var config = {
	apiUrl:'https://mailerapi.criselgeek.com/mailer/v1/'
}

let rules = {
	name: {
		empty: {},
		len: {min: 3, max: 50}
	},
	email: {
		empty: {},
		email: null
	},
	attachments: {
		empty: {},
		json: {}
	}
}

window.onload = () => {
	new CSRFHash('#contactForm', config.apiUrl).Get()
	new FormProcess('#contactForm', config.apiUrl, rules).Send()
}
