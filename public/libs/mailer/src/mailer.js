import axios from 'axios'
import validator from 'validator'
import bootstrap from 'bootstrap'

export class FormProcess {
	constructor(formId, config, rules) {
		this.apiUrl = config.apiUrl
		this.rules = rules
		this.config = config
		this.form = document.querySelector(formId)
		this.attachments = null
		this.loadingElement = this.form.querySelector('#form-loading')
		this.formMessageElement = this.form.querySelector('#form-message')
		this.formMessageTitle = this.formMessageElement.querySelector('#form-message-title')
		this.formMessageText = this.formMessageElement.querySelector('#form-message-text')
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
			this.loadingElement.classList.remove('spinner-show')
		} else {
			this.PostData(fields)
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
				this.loadingElement.classList.add('spinner-show')
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
					this.loadingElement.classList.remove('spinner-show')
				}).catch(error => {
					if(this.attachments && this.attachments[fieldName] !== undefined && this.attachments[fieldName]) {
						delete this.attachments[fieldName]
						this.formMessageElement.classList.add('alert-danger')
					}
					this.loadingElement.classList.remove('spinner-show')
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
		if(data !== false) {
			axios.post(this.apiUrl + 'contact', data)
				.then(response => {
					this.showFormMessages(this.config.messages.success)
					this.form.reset()
				}).catch(error => {
					console.log(error)
					this.showFormMessages(this.config.messages.failed, false)
				})
		} else {
			this.showFormMessages(this.config.messages.failed, false)
		}
	}

	showFormMessages(message, success = true) {
		let alertClassName = null
		switch(success) {
			case false:
				alertClassName = 'alert-danger'
				break
			default:
				alertClassName = 'alert-success'
				break
		}
		this.loadingElement.classList.remove('spinner-show')
		this.formMessageTitle.innerText = message.title
		this.formMessageText.innerText = message.text
		this.formMessageElement.classList.add(alertClassName)
	}

	cleanErrorMessages() {
		let alertFields = this.form.querySelectorAll('#input-error')
		alertFields.forEach(field => {
			field.innerText = ''
			field.style.display = 'none'
		})
		this.formMessageTitle.innerText = ''
		this.formMessageText.innerText = ''
		this.formMessageElement.classList.remove('alert-danger')
		this.formMessageElement.classList.remove('alert-success')
	}

	Send() {
		this.loadingElement.classList.remove('spinner-show')
		this.attachmentUpload()
		this.cleanErrorMessages()
		this.form.addEventListener('click', () => {
			this.cleanErrorMessages()
		})
		this.form.addEventListener('submit', e => {
			e.preventDefault()
			this.loadingElement.classList.add('spinner-show')
			this.cleanErrorMessages()
			this.GetFormFields()
		})
	}
}

export class CSRFHash {
	constructor(formId, config) {
		this.apiUrl = config.apiUrl
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
