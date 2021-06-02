import axios from 'axios'

class FormProcess {
	constructor(formId, apiUrl) {
		this.apiUrl = apiUrl
		this.form = document.querySelector(formId)
		this.attachments = {}
	}

	GetFormFields() {
		var data = new FormData(this.form)
		data.append('attachments', JSON.stringify(this.attachments))
		var entries = data.entries()
		var fields = {}
		for (var entry of entries) {
			for (var attach in this.attachments) {
				if(entry[0] !== attach) {
					fields[entry[0]] = entry[1]
				}
			}
		}
		return fields
	}

	attachmentUpload() {
		let files = this.form.querySelectorAll('input[type="file"]')
		files.forEach(file => {
			file.addEventListener('change', e => {
				let hash = this.form.querySelector('input[name="hash"]')
				let fieldName = e.target.getAttribute('name')
				if(this.attachments[fieldName] !== undefined && this.attachments[fieldName]) {
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
					this.attachments[fieldName] = response.data.data.files
				}).catch(error => {
					if(this.attachments[fieldName] !== undefined && this.attachments[fieldName]) {
						delete this.attachments[fieldName]
					}
				})
			})
		})
	}

	Send() {
		this.attachmentUpload()
		this.form.addEventListener('submit', e => {
			e.preventDefault()
			let data = this.GetFormFields()
			axios.post(this.apiUrl + 'contact', data)
				.then(response => {
					console.log(response)
				}).catch(error => {
					console.log(error)
					alert('Ocurrio un error al enviar el formulario')
				})
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
	new CSRFHash('#contactForm', 'https://mailerapi.criselgeek.com/mailer/v1/').Get()
	new FormProcess('#contactForm', 'https://mailerapi.criselgeek.com/mailer/v1/').Send()
}
