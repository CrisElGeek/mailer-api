import {FormProcess, CSRFHash} from './mailer.js'

var config = {
	apiUrl:'https://mailerapi.criselgeek.com/mailer/v1/',
	messages: {
		success: {
			title: 'Mensaje enviado',
			text: 'Tu mensaje ha sido enviado correctamente, pronto nos pondremos en contacto contigo'
		},
		failed: {
			title: 'Ha ocurrido un error',
			text: 'No pudimos enviar su mensaje, por favor, intente de nuevo, si el problema persiste, pongase en contacto con nosotros.'
		}
	}
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
	new CSRFHash('#contactForm', config).Get()
	new FormProcess('#contactForm', config, rules).Send()
}

import './_base.sass'
