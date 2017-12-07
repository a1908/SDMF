function _(s) {
	if (typeof(lang)!='undefined' && lang[s]) {
		return lang[s];
	}
	return s;
}

var lang = {
	thousands_sep: ',',
	"Mandatory field value is missing": 'Отсутствует обязательное значение',
	"Form validation error": 'Ошибка валидации',
	"no rule to check for the field": 'Отсутсвует правило для валидации',
	"Form submitted":"Форма отправлена",
	"Thank you, we'll be in touch shortly!": "Благодарим за обращение, мы с Вами свяжемся в ближайшее время!",
	"Form delivery failure": "Сбой отправки формы",
	"We are sorry, our server is not able to process your request. Please try again in a while, or contact us by other means": "Приносим извинения, наш сервер не смог обработать Ваш запрос. Пожалуйста, повторите его через некоторое время или свяжитесь с нами другим способом.",
	"Identification error": "Ошибка идентификации",
	"We are sorry, your request was not sent due to technical problem. Please reload the page and try again": "Приносим извинения, Ваш запрос не может быть обработан по техническим причинам. Пожалуйста, обновите страницу и повторите отправку формы.",
};
