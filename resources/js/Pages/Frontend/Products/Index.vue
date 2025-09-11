<template>
    <Head title="Products"/>
    <Menu></Menu>
    <Search></Search>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">Fresh fruits shop</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                    @keyup.enter="performSearch"
                                    aria-describedby="search-icon-1"
                                    class="form-control p-3"
                                    placeholder="Search products..."
                                    type="search">
                                <span id="search-icon-1"
                                      class="input-group-text p-3"
                                      @click="performSearch"
                                      style="cursor: pointer;">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>

                        </div>
                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="fruits">Default Sorting:</label>
                                <select
                                    id="fruits"
                                    v-model="sortBy"
                                    @change="applySorting"
                                    class="border-0 form-select-sm bg-light me-3 no-focus-border"
                                    form="fruitform"
                                    name="fruitlist">
                                    <option value="">Default</option>
                                    <option value="name_asc">Name A-Z</option>
                                    <option value="name_desc">Name Z-A</option>
                                    <option value="price_asc">Price Low to High</option>
                                    <option value="price_desc">Price High to Low</option>
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="featured">Featured First</option>
                                    <option value="popularity">Most Popular</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Categories</h4>

                                        <!-- Loading state for categories -->
                                        <div v-if="categoriesLoading" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading categories...</span>
                                            </div>
                                        </div>

                                        <!-- Dynamic Categories as Dropdowns -->
                                        <div v-else class="fruite-categorie">
                                            <!-- All Products option -->
                                            <div class="mb-3 pt-2" style="border-top: 1px solid #eee;">
                                                <div
                                                    :class="{ 'text-primary fw-bold': selectedCategoryId === null }"
                                                    class="d-flex justify-content-between align-items-center fruite-name"
                                                    style="cursor: pointer; padding: 8px 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;"
                                                    @click="clearCategoryFilter()">

                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-list me-2"></i>
                                                        <span>All Products</span>
                                                    </div>

                                                    <span class="text-muted">({{ totalProductsCount }})</span>
                                                </div>
                                            </div>
                                            <template v-for="category in categories" :key="category.id">


                                                <!-- Main Category (Level 1) - Dropdown Toggle -->
                                                <div class="mb-2">
                                                    <div
                                                        :class="{ 'text-primary fw-bold': selectedCategoryId === category.id }"
                                                        class="d-flex justify-content-between align-items-center fruite-name category-header"
                                                        style="cursor: pointer; padding: 8px 12px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;"
                                                        @click="toggleCategory(category.id)">

                                                        <div class="d-flex align-items-center">
                                                            <!-- Expand/Collapse Icon -->
                                                            <i :class="[
                                                                'me-2',
                                                                category.children && category.children.length > 0
                                                                    ? (expandedCategories.includes(category.id) ? 'fas fa-chevron-down' : 'fas fa-chevron-right')
                                                                    : 'fas fa-circle'
                                                            ]" style="font-size: 10px; opacity: 0.7;"></i>

                                                            <!-- Category Icon -->
                                                            <i :class="getCategoryIcon(category.name)" class="me-2"></i>

                                                            <!-- Category Name -->
                                                            <span
                                                                :class="{ 'text-primary fw-bold': selectedCategoryId === category.id }"
                                                                class="category-name"
                                                                @click.stop="filterByCategory(category.id)">
                                                                {{ category.name }}
                                                            </span>
                                                        </div>

                                                        <!-- Total Products Count -->
                                                        <span class="text-muted">({{
                                                                category.total_products_count || 0
                                                            }})</span>
                                                    </div>

                                                    <!-- Subcategories (Level 2) - Collapsible -->
                                                    <div v-if="category.children && category.children.length > 0"
                                                         v-show="expandedCategories.includes(category.id)"
                                                         class="subcategories-container"
                                                         style="margin-left: 20px; margin-top: 8px; border-left: 2px solid #dee2e6; padding-left: 15px;">

                                                        <div v-for="subcategory in category.children"
                                                             :key="subcategory.id"
                                                             class="mb-1">
                                                            <div :class="{ 'text-primary fw-bold': selectedCategoryId === subcategory.id }"
                                                                 class="d-flex justify-content-between align-items-center fruite-name subcategory-item"
                                                                 @click="filterByCategory(subcategory.id)">

                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-circle me-2" style="font-size: 6px; opacity: 0.6;"></i>
                                                                    <span class="text-sm">{{ subcategory.name }}</span>
                                                                </div>

                                                                <span class="text-muted small">({{ subcategory.products_count || 0 }})</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">Price Range</h4>

                                        <!-- Dual Range Slider Container -->
                                        <div class="price-range-container mb-3">
                                            <div class="price-slider-wrapper">
                                                <!-- Background track -->
                                                <div class="price-track-bg"></div>

                                                <!-- Active range track -->
                                                <div class="price-track-active" :style="rangeTrackStyle"></div>

                                                <!-- Min Price Slider -->
                                                <input
                                                    id="minPriceRange"
                                                    v-model.number="priceRange.min"
                                                    @input="handleMinRangeChange"
                                                    type="range"
                                                    min="0"
                                                    max="100"
                                                    step="1"
                                                    class="price-range-input price-range-min">

                                                <!-- Max Price Slider -->
                                                <input
                                                    id="maxPriceRange"
                                                    v-model.number="priceRange.max"
                                                    @input="handleMaxRangeChange"
                                                    type="range"
                                                    min="0"
                                                    max="100"
                                                    step="1"
                                                    class="price-range-input price-range-max">
                                            </div>

                                            <!-- Price Display Only -->
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="price-display">
                                                    ${{ priceRange.min }}
                                                </span>
                                                <span class="text-muted mx-2">to</span>
                                                <span class="price-display">
                                                    ${{ priceRange.max }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Clear Price Filter -->
                                        <div v-if="(priceRange.min > 0) || (priceRange.max < 100)" class="text-center">
                                            <button @click="clearPriceFilter" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times me-1"></i>
                                                Clear Price Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4>Additional</h4>
                                        <div class="mb-2">
                                            <input id="Categories-1" class="me-2" name="Categories-1" type="radio"
                                                   value="Beverages">
                                            <label for="Categories-1"> Organic</label>
                                        </div>
                                        <div class="mb-2">
                                            <input id="Categories-2" class="me-2" name="Categories-1" type="radio"
                                                   value="Beverages">
                                            <label for="Categories-2"> Fresh</label>
                                        </div>
                                        <div class="mb-2">
                                            <input id="Categories-3" class="me-2" name="Categories-1" type="radio"
                                                   value="Beverages">
                                            <label for="Categories-3"> Sales</label>
                                        </div>
                                        <div class="mb-2">
                                            <input id="Categories-4" class="me-2" name="Categories-1" type="radio"
                                                   value="Beverages">
                                            <label for="Categories-4"> Discount</label>
                                        </div>
                                        <div class="mb-2">
                                            <input id="Categories-5" class="me-2" name="Categories-1" type="radio"
                                                   value="Beverages">
                                            <label for="Categories-5"> Expired</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <h4 class="mb-3">Featured products</h4>
                                    <div v-for="featuredProduct in featuredProducts" :key="featuredProduct.id">
                                        <div class="d-flex align-items-center justify-content-start mb-2">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img
                                                    :src="`/${featuredProduct.media?.find(m => m.is_primary)?.file_path || featuredProduct.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                    alt=""
                                                    class="img-fluid rounded h-100" style="object-fit: contain">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">{{ featuredProduct.name || 'Fruits' }}</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div v-if="featuredProduct.sale_price" class="d-flex mb-2 align-items-center">
                                                    <h5  class="fw-bold me-2 mb-0">${{ featuredProduct.sale_price }} / kg</h5>
                                                    <h6 class="text-danger text-decoration-line-through mb-0">${{ featuredProduct.price }}/ kg</h6>
                                                </div>
                                                <div v-else class="d-flex mb-2">
                                                    <h5  class="fw-bold me-2">${{ featuredProduct.price }} / kg</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="position-relative">
                                        <img src="images/img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                        <div class="position-absolute"
                                             style="top: 50%; right: 10px; transform: translateY(-50%);">
                                            <h3 class="text-secondary fw-bold">Fresh <br> Fruits <br> Banner</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <!-- Search & Filter indicators -->
                            <div v-if="searchQuery || selectedCategoryId || sortBy || priceRange.min > 0 || priceRange.max < 100" class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted">Active filters:</span>

                                    <!-- Search filter -->
                                    <span v-if="searchQuery" class="badge bg-primary">
                                        <i class="fas fa-search me-1"></i>
                                        Search: "{{ searchQuery }}"
                                        <button @click="clearSearch" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Category filter -->
                                    <span v-if="selectedCategoryId" class="badge bg-secondary">
                                        <i class="fas fa-tag me-1"></i>
                                        Category: {{ getCategoryName(selectedCategoryId) }}
                                        <button @click="clearCategoryFilter" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Sorting indicator -->
                                    <span v-if="sortBy" class="badge bg-info">
                                        <i class="fas fa-sort me-1"></i>
                                        Sort: {{ getSortLabel(sortBy) }}
                                        <button @click="clearSorting" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>

                                    <!-- Sorting indicator -->
                                    <span v-if="priceRange.min > 0 || priceRange.max < 100" class="badge bg-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        Price range: ${{ getSortLabel(priceRange.min) }} <i class="	fas fa-arrow-right"></i> ${{ getSortLabel(priceRange.max)}}
                                        <button @click="clearPriceRange" class="btn btn-sm p-0 text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                    <!-- Clear all filters -->
                                    <button @click="clearAllFilters" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>
                                        Clear All
                                    </button>
                                </div>
                            </div>                            <!-- Loading indicator for search -->
                            <div v-if="isSearching" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Searching...</span>
                                </div>
                                <small class="text-muted ms-2">Searching products...</small>
                            </div>

                            <!-- Debug Info -->


                            <div class="row g-4 justify-content-center">
                                <!-- Loading State -->
                                <template v-if="loading">
                                    <div class="col-12 text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading products...</p>
                                    </div>
                                </template>

                                <!-- Error State -->
                                <template v-else-if="error">
                                    <div class="col-12">
                                        <div class="alert alert-danger">{{ error }}</div>
                                    </div>
                                </template>

                                <!-- No Products -->
                                <template v-else-if="!Array.isArray(products) || products.length === 0">
                                    <div class="col-12">
                                        <div class="alert alert-warning">No products found</div>
                                    </div>
                                </template>

                                <!-- Dynamic Products -->
                                <template v-else-if="Array.isArray(products) && products.length > 0">
                                    <div v-for="product in products" :key="product.id"
                                         class="col-md-6 col-lg-6 col-xl-4">
                                        <div class="rounded position-relative fruite-item " >
                                                <div class="fruite-img border-secondary" style="border: 1px solid #000;">
                                                    <img
                                                        :alt="product.name"
                                                        :src="`/${product.media?.find(m => m.is_primary)?.file_path || product.media?.[0]?.file_path || 'products/default.jpg'}`"
                                                        class="img-fluid w-100 rounded-top"
                                                    >
                                                </div>
                                                <!-- Nút trái tim thêm vào wishlist -->
                                                <button class="btn btn-outline-danger position-absolute" style="top:10px; right:10px; z-index:2;" @click="toggleWishlist(product.id)" title="Thêm vào yêu thích">
                                                    <i v-if="isInWishlist(product.id)" class="fa fa-heart" style="color: #f00"></i>
                                                    <i v-else class="far fa-heart" style="color: #f00"></i>
                                                </button>
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                     style="top: 10px; left: 10px;">
                                                    {{ product.category?.name || 'Fruits' }}
                                                </div>

                                            <Link :href="route('detail.index', product.id)">
                                                <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                    <h4>{{ product.name }}</h4>
                                                    <p>{{
                                                            product.short_description?.substring(0, 100) || 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt'
                                                        }}...</p>
                                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                                        <p class="text-dark fs-5 fw-bold mb-0">
                                                            <div  v-if="product.variants[0].sale_price"> <span class="text-danger">${{ product.variants[0].sale_price }} / {{ product.variants[0].unit }}</span>  &nbsp; <span class="text-decoration-line-through opacity-75 fs-6"> ${{ product.variants[0].price }}/ {{ product.variants[0].unit }}</span></div>
                                                            <span v-else>${{ product.variants[0].price }} / {{ product.variants[0].unit }}</span>
                                                        </p>
                                                        <button class="btn border border-secondary rounded-pill px-3 text-primary" @click="addToCart(product)" :disabled="addToCartLoading[product.id]">
                                                            <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                                            <span v-if="addToCartLoading[product.id]">Đang thêm...</span>
                                                            <span v-else>Add to cart</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                </template>

                                <!-- Pagination -->
                                <div
                                    v-if="!loading && !error && Array.isArray(products) && products.length > 0 && pagination.last_page > 1"
                                    class="col-12">
                                    <div class="pagination d-flex justify-content-center mt-5">

                                        <!--  Previous Button -->
                                        <a href="#"
                                           @click.prevent="goToPrevPage()"
                                           :class="['rounded', 'me-1', { 'disabled text-muted': pagination.current_page === 1 }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &laquo;
                                        </a>

                                        <!--  First Page -->
                                        <a href="#"
                                           v-if="pagination.current_page > 4"
                                           @click.prevent="goToFirstPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            First
                                        </a>

                                        <!-- Dots if needed -->
                                        <span v-if="pagination.current_page > 4"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
                                            ...
                                        </span>

                                        <!-- Page Numbers -->
                                        <template v-for="page in getVisiblePages()" :key="page">
                                            <a href="#"
                                               @click.prevent="changePage(page)"
                                               :class="['rounded', 'me-1', { 'active bg-primary text-white': page === pagination.current_page }]"
                                               style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                                {{ page }}
                                            </a>
                                        </template>

                                        <span v-if="pagination.current_page < pagination.last_page - 3"
                                              class="rounded me-1"
                                              style="padding: 8px 12px; border: 1px solid #ddd;">
                                            ...
                                        </span>

                                        <!--  Last Page -->
                                        <a href="#"
                                           v-if="pagination.current_page < pagination.last_page - 3"
                                           @click.prevent="goToLastPage()"
                                           class="rounded me-1"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            {{ pagination.last_page }}
                                        </a>

                                        <!--  Next Button -->
                                        <a href="#"
                                           @click.prevent="goToNextPage()"
                                           :class="['rounded', { 'disabled text-muted': pagination.current_page === pagination.last_page }]"
                                           style="padding: 8px 12px; text-decoration: none; border: 1px solid #ddd;">
                                            &raquo;
                                        </a>
                                    </div>

                                    <!--  Pagination Info -->
                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }}
                                            of {{ pagination.total || 0 }} products
                                            (Page {{ pagination.current_page }} of {{ pagination.last_page }})
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->


    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-primary mb-0">Fruitables</h1>
                            <p class="text-secondary mb-0">Fresh products</p>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative mx-auto">
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" placeholder="Your Email"
                                   type="number">
                            <button
                                class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white"
                                style="top: 0; right: 0;"
                                type="submit">Subscribe Now
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i
                                class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i
                                class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Why People Like us!</h4>
                        <p class="mb-4">typesetting, remaining essentially unchanged. It was
                            popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                        <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Shop Info</h4>
                        <a class="btn-link" href="">About Us</a>
                        <a class="btn-link" href="">Contact Us</a>
                        <a class="btn-link" href="">Privacy Policy</a>
                        <a class="btn-link" href="">Terms & Condition</a>
                        <a class="btn-link" href="">Return Policy</a>
                        <a class="btn-link" href="">FAQs & Help</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Account</h4>
                        <a class="btn-link" href="">My Account</a>
                        <a class="btn-link" href="">Shop details</a>
                        <a class="btn-link" href="">Shopping Cart</a>
                        <a class="btn-link" href="">Wishlist</a>
                        <a class="btn-link" href="">Order History</a>
                        <a class="btn-link" href="">International Orders</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Contact</h4>
                        <p>Address: 1429 Netus Rd, NY 48247</p>
                        <p>Email: Example@gmail.com</p>
                        <p>Phone: +0123 4567 8910</p>
                        <p>Payment Accepted</p>
                        <img src="images/img/payment.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Your Site Name</a>, All right reserved.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">
                    <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                    Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a> Distributed By <a
                    class="border-bottom" href="https://themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router } from '@inertiajs/vue3';
import Menu from '../Includes/Menu.vue';
import Search from '../Includes/Search.vue';
import axios from "axios";
import { useCartStore } from '@/stores/cart';
import  { Link } from '@inertiajs/vue3';
axios.defaults.withCredentials = true;
export default {
    components: {
        AuthenticatedLayout,
        Menu,
        Head,
        Search,
        Link,
    },
    props: {
        auth: Object,
        csrf_token: String,
    },
    data() {
        return {
            products: [],
            categories: [],
            featuredProducts: [],
            wishlistIds: [], // Danh sách id sản phẩm trong wishlist
            wishlistProducts: [], // Danh sách sản phẩm wishlist từ database
            user: null, // Thông tin user, nếu có
            selectedCategoryId: null,
            expandedCategories: [],           //  Track expanded categories
            categoriesLoading: false,
            totalProductsCount: 0,
            searchQuery: '',                  // Search query
            searchTimeout: null,              // Debounce timeout
            isSearching: false,              // Search loading state
            sortBy: '',                      // Sorting option
            priceRange: {                    // Price range with default values
                min: 0,
                max: 100
            },
            priceTimeout: null,
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 9,
                total: 0,
                from: 0,
                to: 0
            },
            loading: true,
            error: null,
            addToCartLoading: {},
        }
    },
    async mounted() {

        console.log('🔑 Auth prop:', this.auth);
        // Lấy user từ prop auth

        this.user = this.auth?.user || null;

        try {
            await Promise.all([
                this.fetchProducts(),
                this.fetchCategories(),
                this.fetchFeaturedProducts()
            ]);
        } catch (error) {
            console.error('❌ Error in mounted:', error);
        }

        // Nếu đã đăng nhập, thực hiện merge wishlist từ localStorage vào database
        if (this.user) {
            // Đảm bảo CSRF cookie trước khi gọi API
            console.log('user', this.user);
            try {
                await axios.get('/sanctum/csrf-cookie');
                // Lấy wishlist từ localStorage nếu có
                const localWishlistIds = localStorage.getItem('wishlistIds');
                let merged = false;
                if (localWishlistIds) {
                    const ids = JSON.parse(localWishlistIds);
                    for (const productId of ids) {
                        try {
                            await axios.post('/api/wishlist', { product_id: productId }, {
                                headers: { 'X-XSRF-TOKEN': this.csrf_token || (this.$page && this.$page.props && this.$page.props.csrf_token) || '' }
                            });
                            merged = true;
                        } catch (error) {
                            if (error.response && error.response.status === 409) {
                                // Đã có trong database, không cần thêm
                            } else {
                                console.error('Lỗi khi merge wishlist:', error);
                            }
                        }
                    }
                    // Xóa wishlist local sau khi merge
                    localStorage.removeItem('wishlistIds');
                    localStorage.removeItem('wishlistProducts');
                    if (merged) {
                        this.showNotification('Đã đồng bộ sản phẩm yêu thích khi đăng nhập!', 'success');
                    }
                }
                // Lấy lại wishlist từ database
                const res = await axios.get('/api/wishlist');
                this.wishlistProducts = res.data;
                this.wishlistIds = res.data.map(item => item.id);
                // Lưu wishlist đã merge vào localStorage
                localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                localStorage.setItem('wishlistProducts', JSON.stringify(this.wishlistProducts));
            } catch (e) {
                console.error('❌ Wishlist error:', e);
                this.wishlistIds = [];
                this.wishlistProducts = [];
                localStorage.removeItem('wishlistIds');
                localStorage.removeItem('wishlistProducts');
            }
        } else {
            // Nếu chưa đăng nhập, lấy wishlist từ localStorage
            const localIds = localStorage.getItem('wishlistIds');
            const localProducts = localStorage.getItem('wishlistProducts');
            this.wishlistIds = localIds ? JSON.parse(localIds) : [];
            this.wishlistProducts = localProducts ? JSON.parse(localProducts) : [];
        }

    },
    computed: {
        // Calculate range track fill style
        cartStore() {
            return useCartStore();
        },
        rangeTrackStyle() {
            const min = this.priceRange.min;
            const max = this.priceRange.max;
            const left = (min / 100) * 100;
            const width = ((max - min) / 100) * 100;

            return {
                left: `${left}%`,
                width: `${width}%`
            };
        }
    },
    methods: {
        async fetchProducts(page = 1) {
            console.log('2')
            try {
                this.loading = true;

                let url = `/api/products?page=${page}`;

                // Add category filter
                if (this.selectedCategoryId) {
                    url += `&category_id=${this.selectedCategoryId}`;
                }
                if (this.searchQuery && this.searchQuery.trim()) {
                    url += `&search=${encodeURIComponent(this.searchQuery.trim())}`;
                }
                if (this.sortBy) {
                    url += `&sort=${this.sortBy}`;
                }
                if (this.priceRange.min > 0) {
                    url += `&price_min=${this.priceRange.min}`;
                }
                if (this.priceRange.max < 100) {
                    url += `&price_max=${this.priceRange.max}`;
                }

                const response = await axios.get(url);
                this.products = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page || 1,
                    last_page: response.data.last_page || 1,
                    per_page: response.data.per_page || 9,
                    total: response.data.total || 0,
                    from: response.data.from || 0,
                    to: response.data.to || 0
                }
                console.log('dataa', response.data)

                this.totalProductsCount = response.data.total || 0; // Update total products count


            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.products = []; // Ensure products is always an array
            } finally {
                this.loading = false;
                this.isSearching = false; // ✅ ADD: Stop search loading
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get(`/api/categories`);
                this.categories = response.data.data;

            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.products = []; // Ensure products is always an array
            } finally {
                this.loading = false;
            }
        },
        async fetchFeaturedProducts() {
            try {
                const response = await axios.get('/api/products/featured');
                this.featuredProducts = response.data;
            } catch (error) {
                console.error('API Error:', error); // Debug
                this.error = 'Failed to load products: ' + error.message;
                this.featuredProducts = [];
            }
        },
        // ✅ Method to change page
        async changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
                await this.fetchProducts(page);

                this.$nextTick(() => {
                    document.querySelector('.col-lg-9')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }
        },

        async filterByCategory(categoryId) {
            console.log('1')
            this.selectedCategoryId = categoryId;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);

            // Scroll to products section
            this.$nextTick(() => {
                document.querySelector('.col-lg-9')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        },

        async clearCategoryFilter() {
            this.selectedCategoryId = null;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        getCategoryIcon(categoryName) {
            const icons = {
                'Fresh Fruits': 'fas fa-apple-alt',
                'Fresh Vegetables': 'fas fa-carrot',
                'Dried Products': 'fas fa-seedling',
                'Jam Products': 'fas fa-jar',
                'Berries': 'fas fa-berry',
                'Citrus': 'fas fa-lemon',
                'Stone Fruits': 'fas fa-peach',
                'Tropical': 'fas fa-palm-tree',
                'Others': 'fas fa-apple-alt'
            };

            return icons[categoryName] || 'fas fa-apple-alt';
        },

        handleMinRangeChange() {
            // Ensure min doesn't exceed max
            if (parseInt(this.priceRange.min) >= parseInt(this.priceRange.max)) {
                this.priceRange.min = this.priceRange.max - 1;
            }
            this.debouncePriceFilter();
        },

        handleMaxRangeChange() {
            // Ensure max doesn't go below min
            if (parseInt(this.priceRange.max) <= parseInt(this.priceRange.min)) {
                this.priceRange.max = parseInt(this.priceRange.min) + 1;
            }
            this.debouncePriceFilter();
        },

        debouncePriceFilter() {
            if (this.priceTimeout) {
                clearTimeout(this.priceTimeout);
            }

            this.priceTimeout = setTimeout(() => {
                this.applyPriceFilter();
            }, 800);
        },

        async applyPriceFilter() {
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        async clearPriceFilter() {
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            await this.applyPriceFilter();
        },


        toggleCategory(categoryId) {
            const index = this.expandedCategories.indexOf(categoryId);
            if (index > -1) {
                // Collapse category
                this.expandedCategories.splice(index, 1);
            } else {
                // Expand category
                this.expandedCategories.push(categoryId);
            }
        },

        async goToFirstPage() {
            await this.changePage(1);
        },

        async goToLastPage() {
            await this.changePage(this.pagination.last_page);
        },

        async goToNextPage() {
            await this.changePage(this.pagination.current_page + 1);
        },

        async goToPrevPage() {
            await this.changePage(this.pagination.current_page - 1);
        },

        getVisiblePages() {
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];

            // Calculate start and end
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);

            // Adjust if we're near the beginning
            if (current <= 3) {
                end = Math.min(last, 5);
            }

            // Adjust if we're near the end
            if (current >= last - 2) {
                start = Math.max(1, last - 4);
            }

            // Generate page numbers
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            return pages;
        },

        // Handle search with debounce
        handleSearch() {
            // Clear existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            this.isSearching = true;

            // Set new timeout for debounced search
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500); // Wait 500ms after user stops typing
        },

        // Perform search
        async performSearch() {
            console.log('🔍 Searching for:', this.searchQuery);
            this.pagination.current_page = 1; // Reset to first page
            await this.fetchProducts(1);
        },

        //  Clear search
        async clearSearch() {
            this.searchQuery = '';
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        // Get category name by ID
        getCategoryName(categoryId) {
            const findCategory = (categories, id) => {
                for (const category of categories) {
                    if (category.id === id) return category.name;
                    if (category.children && category.children.length > 0) {
                        const found = findCategory(category.children, id);
                        if (found) return found;
                    }
                }
                return null;
            };

            return findCategory(this.categories, categoryId) || 'Unknown';
        },

        // Clear all filters
        async clearAllFilters() {
            this.searchQuery = '';
            this.selectedCategoryId = null;
            this.sortBy = '';
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },

        //  Apply sorting
        async applySorting() {
            console.log('🔄 Applying sort:', this.sortBy);
            this.pagination.current_page = 1; // Reset to first page
            await this.fetchProducts(1);

            // Scroll to products section
            this.$nextTick(() => {
                document.querySelector('.col-lg-9')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        },

        // Get sort label for display
        getSortLabel(sortValue) {
            const labels = {
                'name_asc': 'Name A-Z',
                'name_desc': 'Name Z-A',
                'price_asc': 'Price Low to High',
                'price_desc': 'Price High to Low',
                'newest': 'Newest First',
                'oldest': 'Oldest First',
                'featured': 'Featured First',
                'popularity': 'Most Popular'
            };
            return labels[sortValue] || sortValue;
        },

        //  Clear sorting
        async clearSorting() {
            this.sortBy = '';
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },
        //  Clear price range
        async clearPriceRange() {
            this.priceRange.min = 0;
            this.priceRange.max = 100;
            this.pagination.current_page = 1;
            await this.fetchProducts(1);
        },
        isInWishlist(productId) {
            return this.wishlistIds.includes(productId);
        },
        async toggleWishlist(productId) {
            if (this.user) {
                const token = this.csrf_token || (this.$page && this.$page.props && this.$page.props.csrf_token) || '';
                if (!token) {
                    this.showNotification('Không lấy được CSRF token, vui lòng tải lại trang!', 'error');
                    return;
                }
                if (this.isInWishlist(productId)) {
                    try {
                        await axios.delete(`/api/wishlist/${productId}`, {
                            headers: { 'X-XSRF-TOKEN': token }
                        });
                        this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                    } catch (error) {
                        this.showNotification('Xóa khỏi yêu thích thất bại!', 'error');
                        console.error(error);
                    }
                } else {
                    try {
                        await axios.post('/api/wishlist', { product_id: productId }, {
                            headers: { 'X-XSRF-TOKEN': token }
                        });
                        this.wishlistIds.push(productId);
                    } catch (error) {
                        if (error.response && error.response.status === 409) {
                            this.showNotification('Sản phẩm đã có trong danh sách yêu thích!', 'warning');
                        } else {
                            this.showNotification('Thêm vào yêu thích thất bại!', 'error');
                        }
                        console.error(error);
                    }
                }
                // Đồng bộ lại localStorage
                localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                // Lấy lại chi tiết sản phẩm từ database
                try {
                    const res = await axios.get('/api/wishlist');
                    this.wishlistProducts = res.data;
                    localStorage.setItem('wishlistProducts', JSON.stringify(this.wishlistProducts));
                } catch {
                    this.wishlistProducts = [];
                }
            } else {
                let wishlistProducts = [];
                const localProducts = localStorage.getItem('wishlistProducts');
                if (localProducts) {
                    wishlistProducts = JSON.parse(localProducts);
                }
                if (this.isInWishlist(productId)) {
                    this.wishlistIds = this.wishlistIds.filter(id => id !== productId);
                    wishlistProducts = wishlistProducts.filter(p => p.id !== productId);
                } else {
                    this.wishlistIds.push(productId);
                    const product = this.products.find(p => p.id === productId);
                    if (product && !wishlistProducts.some(p => p.id === productId)) {
                        wishlistProducts.push(product);
                    }
                }
                localStorage.setItem('wishlistIds', JSON.stringify(this.wishlistIds));
                localStorage.setItem('wishlistProducts', JSON.stringify(wishlistProducts));
                this.wishlistProducts = wishlistProducts;
            }
        },
        showNotification(message, type) {
            console.log(`Notification (${type}): ${message}`);
            alert(message);
        },

        async addToCart(product) {
            this.addToCartLoading[product.id] = true;
            try {
                if (!this.user) {
                    this.showNotification('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng!', 'error');
                    return;
                }
                // Sử dụng store để thêm vào giỏ hàng và cập nhật count
                await this.cartStore.addToCart(product.id, 1);
                this.showNotification('Đã thêm sản phẩm vào giỏ hàng thành công!', 'success');
            } catch (error) {
                console.error('Add to cart error:', error);
                this.showNotification('Thêm vào giỏ hàng thất bại!', 'error');
            } finally {
                this.addToCartLoading[product.id] = false;
            }
        }
    },
}
</script>

<style lang="scss" scoped>

</style>
