if(typeof dashboard !== 'undefined'){

document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('#card-row .card');
    var cardsTitle = document.querySelectorAll('#card-row .card .card-title');
    var maxCardHeight = 0;
    var maxCardTitleHeight = 0;

    cards.forEach(function (card) {
        var cardHeight = card.offsetHeight;
        if (cardHeight > maxCardHeight) {
            maxCardHeight = cardHeight;
        }
    });

    cards.forEach(function (card) {
        card.style.height = maxCardHeight + 'px';
    });

    cardsTitle.forEach(function (title) {
        var cardTitleHeight = title.offsetHeight;
        if (cardTitleHeight > maxCardTitleHeight) {
            maxCardTitleHeight = cardTitleHeight;
        }
    });

    cardsTitle.forEach(function (title) {
        title.style.height = maxCardTitleHeight + 'px';
    });
    
    // for order the running sprint first
    orderByRunningSprint();

});

// order the running sprint first of list view
function orderByRunningSprint(order="") {
    var productList = document.getElementById('productList');
    var items = Array.from(productList.getElementsByClassName('product'));
    const emptyContent = document.getElementById('empty');
    var onTrackSprintItems = [];
    var delayedSprintItems = [];
    var noRunningSprintItems = [];
    
    items.forEach(function(item) {
        var sprintButtons = item.querySelectorAll('.cls-active-sprints .badge');
        var hasNoRunningSprint = false;
        var hasDelayedSprint = false;
        var hasOnTrackSprint = false;

        sprintButtons.forEach(function(button) {
            if (button.name.includes('No Running Sprint')) {
                hasNoRunningSprint = true;
            } else if (button.name.includes('delayed')) {
                hasDelayedSprint = true;
            } else {
                hasOnTrackSprint = true;
            }
        });

        if (hasNoRunningSprint) {
            noRunningSprintItems.push(item);
        } else if (hasDelayedSprint) {
            delayedSprintItems.push(item);
        } else if (hasOnTrackSprint) {
            onTrackSprintItems.push(item);
        }
    });
    
    // Clear the current list

    // Append the items in the desired order
    if(order == ""){
        if (delayedSprintItems.length === 0 && noRunningSprintItems.length === 0 && onTrackSprintItems.length === 0) {
            emptyContent.style.display = 'block';
        } else {
            emptyContent.style.display = 'none';
        }
        productList.innerHTML = '';
        delayedSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');
            productList.appendChild(item);
        });
        onTrackSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');
            productList.appendChild(item);
        });
        noRunningSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');
            productList.appendChild(item);
        });
    }
    else if(order == 'delayed'){
        // Check if onTrackSprintItems is empty
        if (delayedSprintItems.length === 0) {
            emptyContent.style.display = 'block';
        } else {
            emptyContent.style.display = 'none';
        }
        delayedSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');
        });
        onTrackSprintItems.forEach(function(item) {
            item.classList.add('product-hide');
        });
        noRunningSprintItems.forEach(function(item) {
            item.classList.add('product-hide');
        });
    }
    else if(order == 'onTrack'){
        // Check if onTrackSprintItems is empty
        if (onTrackSprintItems.length === 0) {
            emptyContent.style.display = 'block';
        } else {
            emptyContent.style.display = 'none';
        }
        onTrackSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');            
        });
        delayedSprintItems.forEach(function(item) {
            item.classList.add('product-hide');
        });
        noRunningSprintItems.forEach(function(item) {
            item.classList.add('product-hide');
        });
    }
    else if(order == 'active'){
        // Check if onTrackSprintItems is empty
        if (onTrackSprintItems.length === 0 && delayedSprintItems.length === 0) {
            emptyContent.style.display = 'block';
        } else {
            emptyContent.style.display = 'none';
        }
        onTrackSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');            
        });
        delayedSprintItems.forEach(function(item) {
            item.classList.remove('product-hide');
        });
        noRunningSprintItems.forEach(function(item) {
            item.classList.add('product-hide');
        });
    }
    
};

// to filter the product list and reorder the list according to user search
function filterAndReorderProducts() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const productList = document.getElementById('productList');
    const emptyContent = document.getElementById('empty');
    const products = Array.from(productList.getElementsByClassName('product'));
    
    // order the running sprint first
    if(searchInput === ''){
        orderByRunningSprint();
    }

    let isAnyVisible = false; // Flag to check if any card is visible

    // Highlight the matching product names
    products.forEach(product => {
        const name = product.getAttribute('data-name').toLowerCase();
        if (name.includes(searchInput)) {
            product.classList.remove('product-hide');
            isAnyVisible = true; // Set flag to true if a card is visible
        } else {
            product.classList.add('product-hide');
        }
    });

    // Display the 'No Such Products Found' message if no cards are visible
    if (!isAnyVisible) {
        emptyContent.style.display = 'block';
    } else {
        emptyContent.style.display = 'none';
    }
}




}