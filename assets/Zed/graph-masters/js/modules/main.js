'use strict';

require('ZedGui');
require('../../sass/main.scss');

$(document).ready(function() {
    document
        .querySelector('.add-opening-time-item')
        .addEventListener('click', addOpeningTimeItem);

    const openingTimeCollection = document.querySelector('.opening-time-collection');

    function addOpeningTimeItem(event) {
        event.preventDefault();

        addItem(event, openingTimeCollection);
    }

    document
        .querySelector('.add-commissioning-time-item')
        .addEventListener('click', addCommissioningTimeItem);

    const commissioningTimeCollection = document.querySelector('.commissioning-time-collection');

    function addCommissioningTimeItem(event) {
        event.preventDefault();

        addItem(event, commissioningTimeCollection);
    }

    function addItem(event, collection) {
        collection.insertAdjacentHTML('beforeend', collection
            .dataset
            .prototype
            .replace(
                /__name__/g,
                collection.dataset.index
            )
        );

        collection.dataset.index++;

        collection
            .querySelector('.item:last-child .remove-item')
            .addEventListener('click', removeItem);
    }

    document
        .querySelectorAll('.remove-item')
        .forEach(button => button.addEventListener('click', removeItem));

    function removeItem(event) {
        event.target.closest('.item').remove();
    }
});
