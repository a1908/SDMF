function _(s) {
	if (typeof(lang)!='undefined' && lang[s]) {
		return lang[s];
	}
	return s;
}

var lang = {
	thousands_sep: ' ',
	"no rule to check for the field": 'Отсутствует правило для валидации',
	"Form submitted":"Форма отправлена",
	"Thank you, we'll be in touch shortly!": "Благодарим за обращение, мы с Вами свяжемся в ближайшее время!",
	"Form delivery failure": "Сбой отправки формы",
	"We are sorry, our server is not able to process your request. Please try again in a while, or contact us by other means": "Приносим извинения, наш сервер не смог обработать Ваш запрос. Пожалуйста, повторите его через некоторое время или свяжитесь с нами другим способом.",
	"Identification error": "Ошибка идентификации",
	"We are sorry, your request was not sent due to technical problem. Please reload the page and try again": "Приносим извинения, Ваш запрос не может быть обработан по техническим причинам. Пожалуйста, обновите страницу и повторите отправку формы.",
	"modal-title-Required-field-is-missing":"Отсутствует обязательная информация",
	"modal-title-Form-validation-error":"Ошибка валидации",
	"form-error-Name-is-missing":"Не указано имя",
	"form-error-Email-is-missing":"Не указан емейл",
	"form-error-Invalid-email":"Некорректный емейл",
	"form-error-Phone-is-missing":"Не указан телефон",
	"form-error-Invalid-phone-number":"Некорректный номер телефона",
	"form-error-No-contacts-are-provided":"Не указаны контакты для связи",
	"form-error-Message-is-missing":"Отсутствует сообщение",
	"form-error-Login-is_missing":"Логин не указан",
	"form-error-Mandatory-field-is-missing-default":"Отсутствует обязательное поле",
	"form-error-Incorrect-input-default":"Введенные данные не соответствуют формату поля",
	"are you sure?":"Вы уверены?",
	"Catalogue-error-can-not-delete-Subsections-exist":"Удаление невозможно, присутствуют подразделы",
};