import axios from 'axios'
import validator from 'validator'

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

class FormProcess {
	constructor(formId, apiUrl, rules) {
		this.apiUrl = apiUrl
		this.rules = rules
		this.form = document.querySelector(formId)
		this.attachments = null
	}

	GetFormFields() {
		let data = new FormData(this.form)
		data.append('attachments', JSON.stringify(this.attachments))
		let entries = data.entries()
		let fields = {}
		let errors = []
		let attachKeys = this.attachments ? Object.keys(this.attachments) : []
		for (var entry of entries) {
			if(attachKeys.indexOf(entry[0]) == -1) {
				let result = this.inputValidation(entry[0], entry[1])
				if(result === true) {
					fields[entry[0]] = entry[1]
				} else {
					errors.push(result)
				}
			}
		}
		if(errors.length > 0) {
			this.displayErrors(errors)
		} else {
			this.PostData()
		}
	}

	displayErrors(errors) {
		errors.forEach(error => {
			let alertField = this.form.querySelector('[data-id="' + error.field  + '"]')
			if(alertField) {
				alertField.innerText = error.message
				alertField.style.display = 'block'
			}
		})
	}

	attachmentUpload() {
		let files = this.form.querySelectorAll('input[type="file"]')
		files.forEach(file => {
			file.addEventListener('change', e => {
				let hash = this.form.querySelector('input[name="hash"]')
				let fieldName = e.target.getAttribute('name')
				if(this.attachments && this.attachments[fieldName] !== undefined && this.attachments[fieldName]) {
					delete this.attachments[fieldName]
				}
				let data = new FormData()
				data.append('hash', hash.value)
				data.append(fieldName,  e.target.files[0])
				axios({
					headers: {'content-type': 'multipart/form-data'},
					url: this.apiUrl + 'uploads',
					method: 'POST',
					data: data
				}).then(response => {
					this.attachments = !this.attachments ? {} : null
					this.attachments[fieldName] = response.data.data.files
				}).catch(error => {
					if(this.attachments && this.attachments[fieldName] !== undefined && this.attachments[fieldName]) {
						delete this.attachments[fieldName]
					}
				})
			})
		})
	}

	inputValidation(field, value) {
		let result = true
		for(let rule in this.rules) {
			if(rule === field) {
				for(let param in this.rules[rule]) {
					switch(param) {
						case 'email':
							result = validator.isEmail(value) ? true : {param: param, field: field, message: `El campo ${field} no tiene el formato correcto ${param}`}
							break
						case 'json':
							result = validator.isJSON(value, this.rules[rule][param]) ? true : {param: param, field: field, message: `El formato de este campo no es correcto`}
							break
						case 'len':
							result = validator.isLength(value, this.rules[rule][param]) ? true : {param: param, field: field, message: `El campo ${field} no cumple con el límite máximo o mínimo de caracteres`}
							break
						case 'empty':
							result = !validator.isEmpty(value, this.rules[rule][param]) ? true : {param: param, field: field, message: `El campo ${field} no debe estar vacio`}
							break
					}
				}
			}
		}
		return result
	}

	PostData(data) {
		if(data === true) {
			axios.post(this.apiUrl + 'contact', data)
				.then(response => {
					console.log(response)
				}).catch(error => {
					console.log(error)
					alert('Ocurrio un error al enviar el formulario')
				})
		}
	}

	cleanErrorMessages() {
		let alertFields = this.form.querySelectorAll('#input-error')
		alertFields.forEach(field => {
			field.innerText = ''
			field.style.display = 'none'
		})
	}

	Send() {
		this.attachmentUpload()
		this.cleanErrorMessages()
		this.form.addEventListener('click', () => {
			this.cleanErrorMessages()
		})
		this.form.addEventListener('submit', e => {
			e.preventDefault()
			this.cleanErrorMessages()
			this.GetFormFields()
		})
	}
}

class CSRFHash {
	constructor(formId, apiUrl) {
		this.apiUrl = apiUrl
		this.form = document.querySelector(formId)
	}

	CreateHiddenInput(hash) {
		let input = document.createElement('INPUT')
		input.value = hash
		input.setAttribute('name', 'hash')
		input.setAttribute('type', 'hidden')
		this.form.appendChild(input)
	}

	Get() {
		axios.get(this.apiUrl + 'csrf')
			.then(response => {
				this.CreateHiddenInput(response.data.data.hash)
			}).catch(error => {
				console.error(error)
				alert('Ha ocurrido un error, posiblemente no funcione este formulario')
			})
	}
}

window.onload = () => {
	new CSRFHash('#contactForm', config.apiUrl).Get()
	new FormProcess('#contactForm', config.apiUrl, rules).Send()
}
