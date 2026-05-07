<?
// Подключаемся к БД
include __DIR__ . '/functions.php';
try {
    DBconnect();
    global $link;
    
    // Получаем все продукты из БД
    $query = "SELECT id, name, description, price, image_path FROM products ORDER BY id DESC";
    $result = mysqli_query($link, $query);
    
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $products[] = $row;
        }
    }
} catch (Exception $e) {
    $products = [];
}

// Функция для обрезки текста с добавлением троеточия
function truncateText($text, $maxLength = 30) {
    // Проверяем, что текст не пустой
    if (empty($text) || trim($text) === '') {
        return '';
    }
    
    // Убираем лишние пробелы
    $text = trim($text);
    
    // Проверяем длину текста
    $textLength = mb_strlen($text, 'UTF-8');
    if ($textLength <= $maxLength) {
        return $text;
    }
    
    // Обрезаем текст и добавляем троеточие
    return mb_substr($text, 0, $maxLength, 'UTF-8') . '...';
}
?>

<!-- Header -->
<div class="header">
    <div class="container">
        <div class="header_content">
            <div class="logo">
                <img src="img/logo.png" alt="Logo">
            </div>
            <div class="header_actions">
                <div class="search_wrapper">
                    <img src="img/icons/magnifying.png" alt="Buscar" class="search_icon" id="searchIcon">
                    <div class="search_container" id="searchContainer">
                        <input type="text" placeholder="Buscar..." class="search_input" id="searchInput">
                        <span class="search_clear" id="searchClear">×</span>
                    </div>
                </div>
                <img src="img/icons/basket.png" alt="Carrito" class="cart_icon">
            </div>
        </div>
    </div>
</div>
<!-- Header -->
 
<!-- Banner -->
<section class="banner">
    <div class="container">
        <div class="banner_content">
            <h2 class="banner_title">Bienvenido a Racing Marketplace</h2>
            <p class="banner_subtitle">Los mejores productos para carreras y automovilismo</p>
            <button class="banner_btn">Ver catálogo</button>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products_section">
    <div class="container">
        <h2 class="section_title">Nuestros productos</h2>
        <div class="products_grid">
            <?
            if (empty($products)) {
                echo '<div class="no-products">No hay productos disponibles</div>';
            } else {
                foreach ($products as $product) {
                    $productId = intval($product['id']);
                    $productName = htmlspecialchars($product['name'] ?? 'Sin nombre');
                    $productDescriptionFull = $product['description'] ?? '';
                    // Сначала обрезаем до 30 символов, потом экранируем HTML
                    $productDescription = htmlspecialchars(truncateText($productDescriptionFull, 30));
                    $productPrice = number_format(floatval($product['price'] ?? 0), 2, ',', '.');
                    
                    // Формируем путь к изображению
                    $imagePath = '';
                    if (!empty($product['image_path'])) {
                        $imagePath = 'uploads/' . htmlspecialchars($product['image_path']);
                    } else {
                        // Если нет изображения, используем placeholder
                        $imagePath = 'img/products/placeholder.png';
                    }
                    
                    echo '
                    <div class="product_card">
                        <div class="product_image">
                            <img src="' . $imagePath . '" alt="' . $productName . '" onerror="this.src=\'img/products/placeholder.png\'">
                        </div>
                        <div class="product_info">
                            <h3 class="product_name">' . $productName . '</h3>
                            <p class="product_description">' . $productDescription . '</p>
                            <div class="product_footer">
                                <span class="product_price">$ ' . $productPrice . '</span>
                                <button class="add_to_cart_btn">Agregar al carrito</button>
                            </div>
                        </div>
                    </div>
                    ';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <!-- Subscription Section -->
        <div class="footer_subscribe">
            <h2 class="subscribe_title">SUSCRIBITE</h2>
            <p class="subscribe_text">Recibí nuestras ofertas! Ingresá tu email y recibirás en tu correo novedades y descuentos:</p>
            <form class="subscribe_form">
                <input type="email" placeholder="Email" class="subscribe_input">
                <button type="submit" class="subscribe_arrow">→</button>
            </form>
        </div>

        <!-- Social Media Icons -->
        <div class="footer_social">
            <img src="img/icons/icon_facebook.png" alt="Facebook" class="social_icon">
            <img src="img/icons/icon_instagram.png" alt="Instagram" class="social_icon">
            <img src="img/icons/icon_twitter.png" alt="Twitter" class="social_icon">
        </div>

        <!-- Consumer Protection -->
        <div class="footer_consumer">
            <p>Defensa de las y los Consumidores: Para reclamos Ingrese aquí</p>
        </div>

        <!-- Footer Bottom -->
        <div class="footer_bottom">
            <p class="copyright">© Locademia 2025 - Todos los derechos reservados</p>
            <div class="footer_logo">
                <img src="img/logo.png" alt="Logo">
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchIcon = document.getElementById('searchIcon');
    const searchContainer = document.getElementById('searchContainer');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');

    // Функция для показа/скрытия крестика
    function toggleClearButton() {
        if (searchInput.value.length > 0) {
            searchClear.classList.add('visible');
        } else {
            searchClear.classList.remove('visible');
        }
    }

    // Обработчик клика на иконку поиска
    searchIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        searchContainer.classList.toggle('active');
        if (searchContainer.classList.contains('active')) {
            setTimeout(() => searchInput.focus(), 100);
        }
    });

    // Обработчик ввода текста
    searchInput.addEventListener('input', function() {
        toggleClearButton();
    });

    // Обработчик клика на крестик
    searchClear.addEventListener('click', function(e) {
        e.stopPropagation();
        searchInput.value = '';
        searchInput.focus();
        toggleClearButton();
    });

    // Закрыть поиск при клике вне его
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target) && e.target !== searchIcon) {
            searchContainer.classList.remove('active');
        }
    });

    // Закрыть поиск при нажатии Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchContainer.classList.remove('active');
        }
    });
});
</script>
