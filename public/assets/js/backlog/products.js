
if (typeof m_ProductBacklog !== 'undefined') {

    document.addEventListener('DOMContentLoaded', function () {
        //const sortSelect = document.getElementById('sortCriteria');
        // const productContainer = document.querySelector('.d-flex.flex-wrap.justify-content-center');

        // sortSelect.addEventListener('change', function() {
        //     const sortBy = this.value;
        //     const products = Array.from(document.querySelectorAll('.product-card'));
        //     console.log(products);
        //     products.sort((a, b) => {
        //         let valueA, valueB;
        //         switch (sortBy) {
        //             case 'backlog_items':
        //                 valueA = parseInt(a.querySelector('.product-detail').textContent);
        //                 valueB = parseInt(b.querySelector('.product-detail').textContent);
        //                 break;
        //             case 'items_in_sprint':
        //                 valueA = parseInt(a.querySelectorAll('.product-detail')[2].textContent);
        //                 valueB = parseInt(b.querySelectorAll('.product-detail')[2].textContent);
        //                 break;
        //             case 'completed_percentage':
        //                 valueA = parseFloat(a.querySelectorAll('.product-detail')[5].textContent);
        //                 valueB = parseFloat(b.querySelectorAll('.product-detail')[5].textContent);
        //                 break;
        //             default:
        //                 return 0;
        //         }

        //         return valueB - valueA;
        //         //If the result is negative, a is sorted before b.
        //         // If the result is positive, b is sorted before a.
        //         // If the result is 0, the order of a and b remains unchanged.
        //     });

        //     // Remove existing products
        //     productContainer.innerHTML = '';

        //     // Append sorted products
        //     products.forEach(product => {
        //         productContainer.appendChild(product);
        //     });
        // });

        // search functionality
        const searchBox = document.querySelector('.search-box');
        const searchBtn = document.getElementById('search_btn');
        const searchInput = document.getElementById('userSearchInput');
        const productCards = document.querySelectorAll('.product-card');
        const emptyContent = document.getElementById('empty');

        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            searchBox.classList.toggle('expanded');
            if (searchBox.classList.contains('expanded')) {
                searchInput.focus();
            }
        });

        document.addEventListener('click', function (e) {
            if (!searchBox.contains(e.target)) {
                searchBox.classList.remove('expanded');
            }
        });

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            let isAnyVisible = false;

            productCards.forEach(card => {
                //Retrives the product name
                const productName = card.querySelector('.product-title').textContent.toLowerCase();

                if (productName.includes(searchTerm)) {
                    card.parentNode.style.display = 'block';
                    isAnyVisible = true;
                } else {
                    card.parentNode.style.display = 'none';
                }
            });

            if (!isAnyVisible) {
                emptyContent.style.display = 'block';
            } else {
                emptyContent.style.display = 'none';
            }
        });
    });

    // if(productCount<=0){
    //     emptyContent.style.display = 'block';   
    // }

}