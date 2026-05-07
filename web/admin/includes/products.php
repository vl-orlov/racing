<?
// Определяем базовый путь, если он еще не определен
if (!isset($basePath)) {
    include __DIR__ . '/path_helper.php';
    $basePath = getAdminBasePath();
}

// Подключаемся к БД если еще не подключены
if (!isset($link)) {
    include getIncludesFilePath('functions.php');
    DBconnect();
}

$query = "SELECT COUNT(id) as cprod FROM products";
$result = mysqli_query($link, $query);

if (!$result) {
    $count = 0;
} else {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = $row['cprod'] ?? 0;
}
?>

<div id="debug"></div>

<!-- Begin Page Content -->

<div class="container-fluid users-admin-container">

	<h1 class="h3 mb-2 text-gray-800">Productos</h1>

	<div class="row">
		<!-- Левая колонка: Список товаров -->
		<div class="col-md-4">
			<div class="card shadow mb-4">
				<div class="card-body">
					<h6 class="m-0 font-weight-bold text-primary py-3">Lista de productos: <?= $count ?></h6>
					
					<!-- Поиск -->
					<div class="adm_busc">
						<input class="adm_busc_input" type="text" id="busc_texto" placeholder="Buscar...">
						<div class="adm_busc_bt" onclick="product_list_by_filter()">Buscar</div>
					</div>

					<!-- Кнопка добавления -->
					<div class="addnew_ico" onclick="product_add_open()">
						<img id="plusIcon" src="<?= $basePath ?>img/plus.png" class="icon-size">
					</div>

					<!-- Форма добавления -->
					<div class="addnew">
						<div class="form-field-group">
							<div class="adm_add_tit">Nombre:</div>
							<div class="adm_add_txt"><input class="add_input" type="text" id="product_name"></div>
						</div>

						<div class="form-field-group">
							<div class="adm_add_tit">Descripción:</div>
							<div class="adm_add_txt"><textarea class="add_input" id="product_description" rows="3"></textarea></div>
						</div>

						<div class="form-field-group">
							<div class="adm_add_tit">Precio:</div>
							<div class="adm_add_txt"><input class="add_input" type="number" step="0.01" id="product_price" value="0.00"></div>
						</div>

						<div class="form-field-group" style="text-align:right; margin-top: 10px;">
							<button type="button" onclick="product_create()" style="background: #00b4d8; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
								Guardar
							</button>
						</div>
					</div>

					<!-- Список товаров -->
					<div class="table-responsive users-simple" id="product_list"></div>

					<div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite"></div>
				</div>
			</div>
		</div>

		<!-- Правая колонка: Детальная форма -->
		<div class="col-md-8">
			<div class="card shadow mb-4">
				<div class="card-body">
					<h6 class="m-0 font-weight-bold text-primary py-3">Datos del Producto</h6>
					<div id="product_detail_form" class="user-detail-form">
						<div class="user-detail-empty">
							<p>Seleccione un producto de la lista para ver sus datos.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<script>
// Определяем базовый путь автоматически
var basePath = window.location.pathname;
var adminPos = basePath.lastIndexOf('/admin');
if (adminPos !== -1) {
    basePath = basePath.substring(0, adminPos + 6); // +6 для '/admin'
} else {
    basePath = basePath.substring(0, basePath.lastIndexOf('/') + 1);
}
if (basePath[basePath.length - 1] !== '/') {
    basePath += '/';
}
// Устанавливаем глобально для использования в других скриптах
window.basePath = basePath;

var currentSelectedProductId = null;

function product_add_open() {
    let st = document.querySelector('.addnew');
    let plusIcon = document.getElementById('plusIcon');

    if (st.style.display === 'flex' || st.style.display === '') {
        st.style.display = 'none';
        plusIcon.src = basePath + "img/plus.png";
    } else {
        st.style.display = 'flex';
        plusIcon.src = basePath + "img/close.png";
    }
}

function product_list(busc) {
	document.getElementById('product_list').innerHTML = '<img class="loading" src="' + basePath + 'img/loading_modern.gif">';

	fetch(basePath + 'includes/products_list_js.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ busc: busc || '' })
	})
	.then(response => response.json()) 
	.then(data => {
		document.getElementById('product_list').innerHTML = data.res;
		document.getElementById('dataTable_info').innerHTML = 'Mostrando: ' + data.cant + ' productos';
		
		// Восстанавливаем выделение если был выбран товар
		if (currentSelectedProductId) {
			highlightProduct(currentSelectedProductId);
		}
	})
	.catch(error => {
		document.getElementById('product_list').innerHTML = '<p style="color:red;">Error al obtener la lista de productos</p>';
	});
}

function highlightProduct(productId) {
	// Убираем выделение со всех строк
	document.querySelectorAll('.product-row').forEach(row => {
		row.style.backgroundColor = '';
	});
	
	// Выделяем выбранный товар
	const rows = document.querySelectorAll(`[id^="product_row"][id*="_${productId}"]`);
	rows.forEach(row => {
		row.style.backgroundColor = '#e3f2fd';
	});
}

function selectProduct(productId) {
	currentSelectedProductId = productId;
	highlightProduct(productId);
	loadProductData(productId);
}

function product_del(id) {
	if (!confirm("¿Está seguro de eliminar este producto?\nNo hay forma de recuperar los datos eliminados")) {
        return;
    }

	fetch(basePath + 'includes/products_del_js.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ id: id })
	})
	.then(response => response.json()) 
	.then(data => {
		if (data.ok === 1) {
			// Удаляем строки из списка
			document.querySelectorAll(`[id*="_${id}"]`).forEach(el => el.style.display = "none");
			
			// Очищаем форму справа если удалили выбранный товар
			if (currentSelectedProductId == id) {
				currentSelectedProductId = null;
				document.getElementById('product_detail_form').innerHTML = '<div class="user-detail-empty"><p>Seleccione un producto de la lista para ver sus datos.</p></div>';
			}

            let countElem = document.getElementById('dataTable_info');
            if (countElem) {
                let countText = countElem.innerText;
                let currentCount = parseInt(countText.split(':')[1].trim());
                if (currentCount > 0) {
                    countElem.innerHTML = 'Mostrando: ' + (currentCount - 1) + ' productos';
                }
            }
		} else {
			document.getElementById('debug').innerHTML = `<p style="color:red;">Error: ${data.err}</p>`;
		}
	})
	.catch(error => {
		document.getElementById('debug').innerHTML = `<p style="color:red;">Error de conexión</p>`;
	});
}

function product_create() {
    let data = {
        name:		document.getElementById('product_name')?.value	|| '',
        description: document.getElementById('product_description')?.value	|| '',
        price:		document.getElementById('product_price')?.value	|| '0.00'
    };

    fetch(basePath + 'includes/products_create_js.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(responseData => {
        if (responseData.ok === 1) {
            document.querySelector('.addnew').style.display = 'none';
            document.getElementById('plusIcon').src = basePath + "img/plus.png";

            document.getElementById('product_name').value = '';
            document.getElementById('product_description').value = '';
            document.getElementById('product_price').value = '0.00';

            product_list('');
        } else {
            document.getElementById('debug').innerHTML = `<p style="color:red;">Error: ${responseData.err || 'Error desconocido'}</p>`;
        }
    })
    .catch(error => {
        document.getElementById('debug').innerHTML = `<p style="color:red;">Error de conexión: ${error.message}</p>`;
    });
}

function product_list_by_filter() {
	let busc = document.getElementById('busc_texto').value;
	product_list(busc);
}

// Загрузка данных товара
function loadProductData(productId) {
	const formContainer = document.getElementById('product_detail_form');
	formContainer.innerHTML = '<div class="text-center p-4"><img class="loading" src="' + basePath + 'img/loading_modern.gif"></div>';
	
	fetch(basePath + 'includes/products_get_js.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ product_id: productId })
	})
	.then(response => {
		const contentType = response.headers.get('content-type');
		if (!contentType || !contentType.includes('application/json')) {
			return response.text().then(text => {
				console.error('Respuesta no es JSON:', text);
				throw new Error('El servidor devolvió una respuesta no válida: ' + text.substring(0, 200));
			});
		}
		return response.json();
	})
	.then(data => {
		if (data.ok === 1 && data.data) {
			displayProductForm(data.data, productId);
		} else {
			formContainer.innerHTML = '<div class="alert alert-danger">Error: ' + (data.err || 'Error desconocido') + '</div>';
		}
	})
	.catch(error => {
		formContainer.innerHTML = '<div class="alert alert-danger">Error de conexión al cargar los datos: ' + error.message + '</div>';
	});
}

// Отображение формы с данными товара
function displayProductForm(data, productId) {
	if (typeof generateProductFormHTML === 'function') {
		const form = generateProductFormHTML(data, productId);
		document.getElementById('product_detail_form').innerHTML = form;
	} else {
		// Если функция еще не загружена, ждем немного
		setTimeout(() => {
			if (typeof generateProductFormHTML === 'function') {
				const form = generateProductFormHTML(data, productId);
				document.getElementById('product_detail_form').innerHTML = form;
			} else {
				document.getElementById('product_detail_form').innerHTML = '<div class="alert alert-danger">Error al cargar el formulario</div>';
			}
		}, 200);
	}
}

product_list('');
</script>

<script src="<?= $basePath ?>js/product_detail_form.js"></script>

