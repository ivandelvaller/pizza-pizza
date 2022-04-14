const toppings = [
    // Traditional
    [
        { name: 'Pepperoni', type: 'traditional', price: 5 },
        { name: 'Ham', type: 'traditional', price: 5 },
        { name: 'Sausage', type: 'traditional', price: 5 },
        { name: 'Bacon', type: 'traditional', price: 5 },
        { name: 'Hawaiian', types: 'traditional', price: 5 },
        { name: 'beef', type: 'traditional', price: 5 },
    ],
    // Vegetarian
    [
        { name: 'Roasted Artichoke', type: 'vegetarian', price: 5 },
        { name: 'Roasted Tomato', type: 'vegetarian', price: 5 },
        { name: 'Roasted Mushroom', type: 'vegetarian', price: 5 },
        { name: 'Roasted Red Pepper', type: 'vegetarian', price: 5 },
        { name: 'Roasted Onion', type: 'vegetarian', price: 5 },
        { name: 'Roasted Garlic', type: 'vegetarian', price: 5 },
        { name: 'Roasted Spinach', type: 'vegetarian', price: 5 },
        { name: 'Roasted Brocolli', type: 'vegetarian', price: 5 },
    ],
    // Exotics
    [
        { name: 'chiken', type: 'excotic', price: 12 },
        { name: 'boneless', type: 'excotic', price: 12 },
        { name: 'angus beef', type: 'excotic', price: 12 },
        { name: 'pork', type: 'excotic', price: 12 },
        { name: 'turkey', type: 'excotic', price: 12 },
        { name: 'shripms', type: 'excotic', price: 12 },
    ],
    // Own
    []
];

$(document).ready(function () {
    // Set default values.
    $('input:radio[name="pizza-button"]')[0].checked = true;
    $('input:radio[name="size-button"]')[1].checked = true;
    $('input:radio[name="dough-button"]')[0].checked = true;

    setPizza(
        $(document).find('input:radio[name="pizza-button"]')[0].value,
    );

    $('#total-price').text(getTotal());

    (async () => {
        await updateCustomtoppings();
    })();
});

$(document).on('click', '.close-step', function () {
    const name = $(this).data('close');
    const elementName = `#mobile-${name}-options`;
    const text = $(this).text();

    if(text === 'hide') {
        $(document).find(elementName).hide();
        $(this).text('show');
        return;
    }

    $(document).find(elementName).show();
    $(this).text('hide');
});

$(document).on('change', 'input:radio[name="pizza-button"]', function () {
    setPizza(this.value);
});

$(document).on('click', '#add-topping-button', async function () {
    $('#add-topping-error').text('');

    const inputValue = $('#add-topping-input').val();
    if(!inputValue?.trim()?.length) {
        $('#add-topping-error').text('Please enter a topping name.');
        return;
    }

    const response = JSON.parse(await addTopping(inputValue));
    if(!response.success) {
        $('#add-topping-error').text('We are having trouble adding your topping. Try later.');
        return;
    }

    await updateCustomtoppings(response.topping);
    $('#add-topping-input').val('');
});

$(document).on('click', '.delete-topping', async function () {
    const id = $(this).data('id');
    const response = await deleteTopping(id);

    if(response.success) {
        $(`#topping-${id}`).removeClass('fade-in');
        $(`#topping-${id}`).addClass('fade-out');
        setTimeout(() => {
            $(`#topping-${id}`).remove();
        }, 500);
    }
    if($('#custom-toppings-container').find('.topping-list-item').length === 0) {
        $('#empty-toppings').show();
    }
})

$(document).on('change', 'input', function () {
    if($(this).prop('type') === 'checkbox' || $(this).prop('type') === 'radio') {
        $('#total-price').text(getTotal());
    }
}) 

$('#modal').on('shown.bs.modal', function () {
    $('#buy-total').text(getTotal());
})

function getTotal() {
    const inputs = $(document).find('input:checked');
    let total = 0;
    $.each(inputs, function(_index, input) {
        total += +$(input).data('price');
    });
    return `$${total.toFixed(2)}`;
}

function setPizza(type) {
    let filteredTipcs = [];
    const toppingListContainer = $(document).find('#topping-list-container').html('');

    switch(type) {
        case 'vegetarian':
            filteredTipcs = [...toppings[1]];
            break;
        case 'traditional':
            filteredTipcs = [...toppings[0]];
            break;
        default:
            filteredTipcs = [...toppings[0], ...toppings[1], ...toppings[2]];
            break;
    }

    $.each(filteredTipcs, function (_index, topping) {
        const toppingElement = $(`
            <div class="topping-list-item">
                <input type='checkbox' name='topping' value='${topping.price}' id="topping-${topping.name}" data-price="${topping.price}">
                <div class="topping-info>
                    <span class="topping-info__name">${topping.name}</span>
                    <span class="topping-info__price">+${topping.price}</span>
                </div>
            <div>
        `);
        toppingListContainer.append(toppingElement);
    });
}

function createToppingElement(topping, isNew = false) {
    return $(`
            <div class="topping-list-item ${isNew ? "fade-in" : null}" id="topping-${topping.id}">
                <input type='checkbox' name='topping' id='topping-${topping.id}' data-price='${topping.price}'">
                <div class="topping-info>
                    <span class="topping-info__name">${topping.name}</span>
                    <span class="topping-info__price">+${topping.price}</span>
                </div>
                <i class="fa-solid fa-trash delete-topping" data-id="${topping.id}" data-toggle="tooltip" data-placement="top" title="Delete ${topping.name} from toppings"></i>
                ${isNew ? `<span class="new-topping">new</span>` : ''}
            <div>
    `);
}

function error() {
    return {
        success: false,
        error: 'Error reaching server',
    };
}

/**
 * Get all custom toppings and update the list.
 * But if new topping is passed, add it to the list.
 */
async function updateCustomtoppings(newTopping = false) {
    $('#add-topping-input').val('');
    if(!newTopping) {
        $(document).find('#own-topping-list-container').text('');

        const { toppings } = await getToppings();
        if(!toppings.length) {
            $('#empty-toppings').show();
            return;
        }

        $('#empty-toppings').hide();
        $.each(toppings, function (_index, topping) {
            createToppingElement(topping).appendTo('#own-topping-list-container');
        });
        return;
    }

    $('#empty-toppings').hide();
    createToppingElement(newTopping, true).prependTo('#own-topping-list-container');
    setTimeout(() => {
        $(`#topping-${newTopping.id}`).parent().removeClass('fade-in');
    }, 500);
}

async function addTopping(topping) {
    return $.ajax({
        url: 'index.php?action=addTopping',
        data: { topping },
        success: function (jsonResponse) {
            console.log('RESPONSE: ', JSON.parse(jsonResponse));
            let response = JSON.parse(jsonResponse);

            if(!response.success) {
                return {
                    success: false,
                    error: response.errormsg,
                };
            }

            return {
                ...response,
            };
        },
        error,
    });
}

async function getToppings() {
    return $.ajax({
        url: 'index.php?action=getToppings',
        dataType: "JSON",
        success: function (json) {
            if(json.success === 0 || !json.toppings) {
                return {
                    success: false,
                    error: 'Error getting toppings. Try later.',
                };
            }
            return {
                success: true,
                toppings: json.toppings,
            };
        },
        error,
    });
}

async function deleteTopping(id) {
    return $.ajax({
        url: `index.php?action=deleteTopping&toppingId=${id}`,
        dataType: 'JSON',
        success: function (result) {
            if(!result.success) {
                return {
                    success: false,
                    error: 'Error deleting topping. Try later.',
                };
            }
            return { success: true };
        },
        error,
    });

}
