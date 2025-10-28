// Функция для работы с localStorage
const FavoriteManager = {
    key: 'userFavorites',
    
    get: function() {
        try {
            return JSON.parse(localStorage.getItem(this.key)) || [];
        } catch (e) {
            return [];
        }
    },
    
    set: function(items) {
        localStorage.setItem(this.key, JSON.stringify(items));
        this.updateCounter();
    },
    
    add: function(id) {
        let items = this.get();
        if (!items.includes(id)) {
            items.push(id);
            this.set(items);
        }
    },
    
    remove: function(id) {
        let items = this.get();
        const index = items.indexOf(id);
        if (index > -1) {
            items.splice(index, 1);
            this.set(items);
        }
    },

    clear: function() {
        localStorage.removeItem(this.key);
    },

    // Обновление счетчика в шапке
    updateCounter: function() {
        const count = this.get().length;
        // Предположим, у вас есть элемент <span id="favorites-counter">
        const counterElement = document.getElementById('favorites-counter');
        if (counterElement) {
            counterElement.textContent = count;
            counterElement.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
};
