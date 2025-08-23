export class FilterUtils {
    // Debounce function
    static debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Get category icon
    static getCategoryIcon(categoryName) {
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
    }

    // Get sort label
    static getSortLabel(sortValue) {
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
    }

    // Find category name by ID
    static findCategoryName(categories, categoryId) {
        const findCategory = (cats, id) => {
            for (const category of cats) {
                if (category.id === id) return category.name;
                if (category.children?.length > 0) {
                    const found = findCategory(category.children, id);
                    if (found) return found;
                }
            }
            return null;
        };
        return findCategory(categories, categoryId) || 'Unknown';
    }

    // Calculate price range track style
    static calculateRangeTrackStyle(min, max) {
        const left = (min / 100) * 100;
        const width = ((max - min) / 100) * 100;
        return {
            left: `${left}%`,
            width: `${width}%`
        };
    }

    // Validate price range
    static validatePriceRange(min, max) {
        const validMin = Math.max(0, Math.min(min, 99));
        const validMax = Math.min(100, Math.max(max, 1));

        if (validMin >= validMax) {
            return {
                min: validMax - 1,
                max: validMax
            };
        }

        return {
            min: validMin,
            max: validMax
        };
    }

    // Scroll to element smoothly
    static scrollToElement(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
}
