/**
 * Serviço de validação de formulários
 * Implementa as mesmas regras dos Requests do backend
 * Validação ocorre apenas no submit
 */

export const validateProduct = (product) => {
  const errors = {};

  // Nome: required, string, min:3, max:255
  if (!product.nome || product.nome.trim() === '') {
    errors.nome = 'O nome do produto é obrigatório';
  } else if (product.nome.length < 3) {
    errors.nome = 'O nome deve ter no mínimo 3 caracteres';
  } else if (product.nome.length > 255) {
    errors.nome = 'O nome deve ter no máximo 255 caracteres';
  }

  // Preço: required, numeric, min:0.01
  if (product.preco_venda === null || product.preco_venda === undefined || product.preco_venda === '') {
    errors.preco_venda = 'O preço de venda é obrigatório';
  } else if (isNaN(product.preco_venda) || product.preco_venda < 0.01) {
    errors.preco_venda = 'O preço deve ser no mínimo R$ 0,01';
  }

  return errors;
};

export const validatePurchase = (purchase) => {
  const errors = {};

  // Fornecedor: required, string, max:255
  if (!purchase.fornecedor || purchase.fornecedor.trim() === '') {
    errors.fornecedor = 'O nome do fornecedor é obrigatório';
  } else if (purchase.fornecedor.length > 255) {
    errors.fornecedor = 'O fornecedor deve ter no máximo 255 caracteres';
  }

  // Produtos: required, array, min:1
  if (!purchase.produtos || purchase.produtos.length === 0) {
    errors.produtos = 'Adicione pelo menos um produto';
  } else {
    // Validar cada item de produto
    const produtoErrors = [];
    purchase.produtos.forEach((item, index) => {
      const itemErrors = {};

      // ID: required, integer
      if (!item.id) {
        itemErrors.id = 'Selecione um produto';
      }

      // Quantidade: required, integer, min:1
      if (item.quantidade === null || item.quantidade === undefined || item.quantidade === '') {
        itemErrors.quantidade = 'A quantidade é obrigatória';
      } else if (!Number.isInteger(Number(item.quantidade)) || item.quantidade < 1) {
        itemErrors.quantidade = 'A quantidade deve ser no mínimo 1';
      }

      // Preço unitário: required, numeric, min:0.01
      if (item.preco_unitario === null || item.preco_unitario === undefined || item.preco_unitario === '') {
        itemErrors.preco_unitario = 'O preço unitário é obrigatório';
      } else if (isNaN(item.preco_unitario) || item.preco_unitario < 0.01) {
        itemErrors.preco_unitario = 'O preço deve ser no mínimo R$ 0,01';
      }

      if (Object.keys(itemErrors).length > 0) {
        produtoErrors[index] = itemErrors;
      }
    });

    if (produtoErrors.length > 0) {
      errors.produtosDetalhes = produtoErrors;
    }
  }

  return errors;
};

export const validateSale = (sale) => {
  const errors = {};

  // Cliente: required, string, max:255
  if (!sale.cliente || sale.cliente.trim() === '') {
    errors.cliente = 'O nome do cliente é obrigatório';
  } else if (sale.cliente.length > 255) {
    errors.cliente = 'O cliente deve ter no máximo 255 caracteres';
  }

  // Produtos: required, array, min:1
  if (!sale.produtos || sale.produtos.length === 0) {
    errors.produtos = 'Adicione pelo menos um produto';
  } else {
    // Validar cada item de produto
    const produtoErrors = [];
    sale.produtos.forEach((item, index) => {
      const itemErrors = {};

      // ID: required, integer
      if (!item.id) {
        itemErrors.id = 'Selecione um produto';
      }

      // Quantidade: required, integer, min:1
      if (item.quantidade === null || item.quantidade === undefined || item.quantidade === '') {
        itemErrors.quantidade = 'A quantidade é obrigatória';
      } else if (!Number.isInteger(Number(item.quantidade)) || item.quantidade < 1) {
        itemErrors.quantidade = 'A quantidade deve ser no mínimo 1';
      }

      // Preço unitário: required, numeric, min:0.01
      if (item.preco_unitario === null || item.preco_unitario === undefined || item.preco_unitario === '') {
        itemErrors.preco_unitario = 'O preço unitário é obrigatório';
      } else if (isNaN(item.preco_unitario) || item.preco_unitario < 0.01) {
        itemErrors.preco_unitario = 'O preço deve ser no mínimo R$ 0,01';
      }

      if (Object.keys(itemErrors).length > 0) {
        produtoErrors[index] = itemErrors;
      }
    });

    if (produtoErrors.length > 0) {
      errors.produtosDetalhes = produtoErrors;
    }
  }

  return errors;
};

/**
 * Verifica se um objeto de erros está vazio
 */
export const isFormValid = (errors) => {
  return Object.keys(errors).length === 0;
};

/**
 * Extrai mensagens de erro da resposta de erro da API
 * e as exibe como lista de toasts
 */
export const handleApiErrors = (errorResponse, toastService) => {
  if (
    !errorResponse.response || 
    !errorResponse.response.data
  ) {
    toastService.error('Erro ao enviar dados');
    return;
  }

  const data = errorResponse.response.data;

  // Se há objeto 'errors' com as mensagens de validação
  if (data.errors) {
    const errors = data.errors;
    
    // Itera sobre os erros e exibe cada um como um toast
    Object.keys(errors).forEach((field) => {
      const messages = errors[field];
      if (Array.isArray(messages)) {
        messages.forEach((message) => {
          toastService.error(message);
        });
      } else {
        toastService.error(messages);
      }
    });
  } else {
    toastService.error((data.message || data.error) || 'Erro ao enviar dados');
  }
};

/**
 * Valida os campos de login
 */
export const validateLogin = (credentials) => {
  const errors = {};

  // Email: required, email, max:255
  if (!credentials.email || credentials.email.trim() === '') {
    errors.email = 'O e-mail é obrigatório';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(credentials.email)) {
    errors.email = 'Informe um e-mail válido';
  } else if (credentials.email.length > 255) {
    errors.email = 'O e-mail deve ter no máximo 255 caracteres';
  }

  // Password: required, string, min:6
  if (!credentials.password) {
    errors.password = 'A senha é obrigatória';
  } else if (credentials.password.length < 6) {
    errors.password = 'A senha deve ter no mínimo 6 caracteres';
  }

  return errors;
};

/**
 * Valida os campos de registro/cadastro
 */
export const validateRegister = (user) => {
  const errors = {};

  // Nome: required, string, min:3, max:255
  if (!user.name || user.name.trim() === '') {
    errors.name = 'O nome é obrigatório';
  } else if (user.name.length < 3) {
    errors.name = 'O nome deve ter no mínimo 3 caracteres';
  } else if (user.name.length > 255) {
    errors.name = 'O nome deve ter no máximo 255 caracteres';
  }

  // Email: required, email, max:255
  if (!user.email || user.email.trim() === '') {
    errors.email = 'O e-mail é obrigatório';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.email)) {
    errors.email = 'Informe um e-mail válido';
  } else if (user.email.length > 255) {
    errors.email = 'O e-mail deve ter no máximo 255 caracteres';
  }

  // Password: required, string, min:6
  if (!user.password) {
    errors.password = 'A senha é obrigatória';
  } else if (user.password.length < 6) {
    errors.password = 'A senha deve ter no mínimo 6 caracteres';
  }

  // Password confirmation: required, same:password
  if (!user.password_confirmation) {
    errors.password_confirmation = 'A confirmação de senha é obrigatória';
  } else if (user.password !== user.password_confirmation) {
    errors.password_confirmation = 'As senhas não conferem';
  }

  return errors;
};
