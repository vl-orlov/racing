// Функции для работы с детальной формой товара

function generateProductFormHTML(data, productId) {
    let html = '<form id="product_detail_form_content" onsubmit="return false;">';
    html += '<input type="hidden" id="form_product_id" value="' + productId + '">';
    
    // Секция: Datos del Producto
    html += '<div class="user-form-section">';
    html += '<h4 class="section-title">Datos del Producto</h4>';
    
    html += '<div class="form-group"><label>Nombre <span class="req">*</span></label>';
    html += '<input type="text" class="form-control" id="form_product_name" value="' + escapeHtml(data.name || '') + '" required></div>';
    
    html += '<div class="form-group"><label>Descripción</label>';
    html += '<textarea class="form-control" id="form_product_description" rows="4">' + escapeHtml(data.description || '') + '</textarea></div>';
    
    html += '<div class="form-group"><label>Precio <span class="req">*</span></label>';
    html += '<input type="number" step="0.01" class="form-control" id="form_product_price" value="' + escapeHtml(data.price || '0.00') + '" required></div>';

    const categories = [
        {value: '',           label: '— Sin categoría —'},
        {value: 'remeras',    label: 'Remeras'},
        {value: 'polo',       label: 'Polos'},
        {value: 'musculosa',  label: 'Musculosas'},
        {value: 'mochila',    label: 'Mochilas'},
        {value: 'botella',    label: 'Botellas'},
        {value: 'bucket',     label: 'Bucket Hats'},
        {value: 'accesorios', label: 'Accesorios'},
    ];
    let opts = categories.map(c =>
        '<option value="' + c.value + '"' + (data.category === c.value ? ' selected' : '') + '>' + c.label + '</option>'
    ).join('');
    html += '<div class="form-group"><label>Categoría</label>';
    html += '<select class="form-control" id="form_product_category">' + opts + '</select></div>';
    
    html += '<div class="form-group"><label>Creado el</label>';
    html += '<div class="readonly-field">' + escapeHtml(data.created_at || '') + '</div></div>';
    
    html += '<div class="form-group"><label>Actualizado el</label>';
    html += '<div class="readonly-field">' + escapeHtml(data.updated_at || '') + '</div></div>';
    
    html += '</div>';
    
    // Секция: Imagen
    html += '<div class="user-form-section">';
    html += '<h4 class="section-title">Imagen del Producto</h4>';
    
    html += '<div class="form-group">';
    html += '<label>Imagen actual</label>';
    
    if (data.image_path) {
        const imageUrl = (typeof basePath !== 'undefined' ? basePath : '../') + '../uploads/' + escapeHtml(data.image_path);
        html += '<div class="file-item-preview">';
        html += '<img src="' + imageUrl + '" alt="Imagen del producto" style="max-width: 300px; max-height: 300px; margin: 10px 0;">';
        html += '<br><a href="' + imageUrl + '" target="_blank">Ver imagen completa</a>';
        html += '</div>';
    } else {
        html += '<div class="alert alert-info">No hay imagen cargada</div>';
    }
    
    html += '</div>';
    
    html += '<div class="form-group">';
    html += '<label>Subir nueva imagen</label>';
    html += '<input type="file" class="form-control-file" id="form_product_image" accept="image/jpeg,image/jpg,image/png,image/gif">';
    html += '<small class="form-text text-muted">Formatos permitidos: JPEG, PNG, GIF. Tamaño máximo: 5MB</small>';
    html += '<div id="upload_progress" style="margin-top: 10px;"></div>';
    html += '</div>';
    
    html += '</div>';
    
    // Кнопка Guardar
    html += '<div class="form-actions">';
    html += '<button type="button" class="btn btn-primary" onclick="saveProductData(' + productId + ')">Guardar</button>';
    html += '<div id="save_message" style="margin-top: 10px;"></div>';
    html += '</div>';
    
    html += '</form>';
    
    // Добавляем обработчик загрузки файла
    setTimeout(() => {
        const fileInput = document.getElementById('form_product_image');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    uploadProductImage(productId, this.files[0]);
                }
            });
        }
    }, 100);
    
    return html;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function saveProductData(productId) {
    const name = document.getElementById('form_product_name')?.value.trim() || '';
    const description = document.getElementById('form_product_description')?.value.trim() || '';
    const price = document.getElementById('form_product_price')?.value || '0.00';
    const category = document.getElementById('form_product_category')?.value || '';
    const imagePath = document.getElementById('form_product_image')?.dataset?.currentPath || '';
    
    if (!name) {
        showMessage('save_message', 'El nombre es obligatorio', 'error');
        return;
    }
    
    const data = {
        product_id: productId,
        name: name,
        description: description,
        price: price || '0.00',
        category: category,
    };
    
    if (imagePath) {
        data.image_path = imagePath;
    }
    
    const saveMessage = document.getElementById('save_message');
    saveMessage.innerHTML = '<div class="alert alert-info">Guardando...</div>';
    
    fetch(basePath + 'includes/products_update_js.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok === 1) {
            showMessage('save_message', 'Datos guardados correctamente', 'success');
            // Перезагружаем данные товара
            setTimeout(() => {
                loadProductData(productId);
            }, 1000);
        } else {
            showMessage('save_message', 'Error: ' + (data.err || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        showMessage('save_message', 'Error de conexión al guardar', 'error');
    });
}

function uploadProductImage(productId, file) {
    const progressDiv = document.getElementById('upload_progress');
    progressDiv.innerHTML = '<div class="alert alert-info">Subiendo imagen...</div>';
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('product_id', productId);
    
    fetch(basePath + 'includes/products_upload_js.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok === 1) {
            progressDiv.innerHTML = '<div class="alert alert-success">Imagen subida correctamente</div>';
            // Обновляем изображение на странице
            if (data.url) {
                const previewDiv = document.querySelector('.file-item-preview');
                if (previewDiv) {
                    previewDiv.innerHTML = '<img src="' + data.url + '" alt="Imagen del producto" style="max-width: 300px; max-height: 300px; margin: 10px 0;"><br><a href="' + data.url + '" target="_blank">Ver imagen completa</a>';
                }
            }
            // Сохраняем путь к изображению для последующего сохранения
            const imageInput = document.getElementById('form_product_image');
            if (imageInput) {
                imageInput.dataset.currentPath = data.file_path;
            }
        } else {
            progressDiv.innerHTML = '<div class="alert alert-danger">Error: ' + (data.err || 'Error al subir la imagen') + '</div>';
        }
    })
    .catch(error => {
        progressDiv.innerHTML = '<div class="alert alert-danger">Error de conexión al subir la imagen</div>';
    });
}

function showMessage(elementId, message, type) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    element.innerHTML = '<div class="alert ' + alertClass + '">' + escapeHtml(message) + '</div>';
}

