const errorTranslations = {
  // Validation errors
  'The customer name field is required.': 'O nome do solicitante é obrigatório.',
  'The customer name may not be greater than 255 characters.': 'O nome do solicitante não pode ter mais de 255 caracteres.',
  'The destination field is required.': 'O destino é obrigatório.',
  'The destination may not be greater than 255 characters.': 'O destino não pode ter mais de 255 caracteres.',
  'The departure date field is required.': 'A data de ida é obrigatória.',
  'The departure date must be a valid date.': 'A data de ida deve ser uma data válida.',
  'The departure date must be a date after or equal to today.': 'A data de ida não pode ser anterior a hoje.',
  'The return date field is required.': 'A data de volta é obrigatória.',
  'The return date must be a valid date.': 'A data de volta deve ser uma data válida.',
  'The return date must be a date after or equal to the departure date.': 'A data de volta deve ser igual ou posterior à data de ida.',
  
  // Business rules
  'The departure date cannot be after the return date.': 'A data de ida não pode ser posterior à data de volta.',
  'A confirmed order cannot be cancelled.': 'Um pedido aprovado não pode ser cancelado.',
  
  // Authorization errors
  'User is not authorized to update the status of this order.': 'Você não tem permissão para atualizar o status deste pedido.',
  'User is not authorized to view this order.': 'Você não tem permissão para visualizar este pedido.',
  
  // Generic errors
  'Erro ao criar pedido': 'Erro ao criar pedido',
  'Erro ao buscar pedidos': 'Erro ao buscar pedidos',
  'Erro ao atualizar status': 'Erro ao atualizar status',
}

export const translateError = (message) => {
  if (!message) return 'Ocorreu um erro inesperado.'
  
  if (errorTranslations[message]) {
    return errorTranslations[message]
  }
  
  for (const [key, value] of Object.entries(errorTranslations)) {
    if (message.includes(key)) {
      return value
    }
  }

  return message
}

export default translateError
