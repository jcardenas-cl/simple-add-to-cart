jQuery( document ).ready( function() {
    jQuery('.cbo-variation').change( function() {
        const me                    = jQuery( this )
        const parent                = jQuery( me ).parent()
        const variations_selected   = jQuery( parent ).find('.cbo-variation')
        let variations_avalilable   = jQuery( parent ).find('.variation-data').val()
        variations_avalilable       = JSON.parse(variations_avalilable)
        // Recorrer las variedades seleccionadas
        let selected_elements       = Array()
        jQuery(variations_selected).each( function() {
            let obj_selected        = {}
            const attribute         = jQuery( this ).attr('name')
            const value             = jQuery( this ).val()
            obj_selected.att_name   = attribute
            obj_selected.att_value  = value
            selected_elements.push(obj_selected)
        })
        
        variations_avalilable.forEach( function(variation) {
            const attributes = variation.attributes
            if ( JSON.stringify(attributes) === JSON.stringify(selected_elements) ) {
                jQuery( parent ).find('.price').html( '$' + variation.price )
                jQuery( parent ).find('.variation-id').val( variation.variation_id )
                return
            }
        });
    });

    jQuery('.satc-add-to-cart').click( function() {
        const me                    = jQuery( this )
        const parent                = jQuery( me ).parent()
        const product_id            = jQuery( parent ).find('.product-id').val()
        const variation_id          = jQuery( parent ).find('.variation-id').val()
        const quantity              = jQuery( parent ).find('.product-quantity').val()
        const variations_selected   = jQuery( parent ).find('.cbo-variation')

        // Validar que haya seleccionado valores para cada variedad
        if ( !has_selected_valid_variations( variations_selected ) ) {
            jQuery( parent ).find('.status-cart').html('Seleccione todas las variedades antes de agregar')
        } else if ( !is_valid_quantity( quantity ) ) {
            jQuery( parent ).find('.status-cart').html('La cantidad debe ser al menos de 1')
        } else {
            jQuery.post(
                site_config.ajaxurl,
                {
                    'action': 'satc_add_to_cart',
                    'product_id': product_id,
                    'variation_id': variation_id,
                    'quantity': quantity
                }, function ( response ) {
                    if ( response.status == 'OK' ) {
                        jQuery( parent ).find('.status-cart').html('Producto agregado con éxito<br> ir a la <a href="/cart">carta</a>')
                    } else {
                        jQuery( parent ).find('.status-cart').html('Algo falló')
                    }
                }
            )
        }

    });
});

function has_selected_valid_variations( variations ) {
    let is_valid = true
    jQuery( variations ).each( function() {
        if ( '-1' === jQuery( this ).val() ) {
            is_valid = false
            return
        }
    })
    return is_valid
}

function is_valid_quantity( quantity ) {
    if ( quantity > 0 ) {
        return true
    }

    return false
}

/*
{
    "attributes": [
        {
            "att_name": "attribute_gramos",
            "att_value": "15g"
        },
        {
            "att_name": "attribute_variedad",
            "att_value": "var 1"
        }
    ],
    "variation_id": 122,
    "price": 2000
},
{
    "attributes": [
        {
            "att_name": "attribute_gramos",
            "att_value": "15g"
        },
        {
            "att_name": "attribute_variedad",
            "att_value": "var 2"
        }
    ],
    "variation_id": 123,
    "price": 3000
},
{
    "attributes": [
        {
            "att_name": "attribute_gramos",
            "att_value": "30g"
        },
        {
            "att_name": "attribute_variedad",
            "att_value": "var 1"
        }
    ],
    "variation_id": 124,
    "price": 4000
},
{
    "attributes": [
        {
            "att_name": "attribute_gramos",
            "att_value": "30g"
        },
        {
            "att_name": "attribute_variedad",
            "att_value": "var 2"
        }
    ],
    "variation_id": 125,
    "price": 5000
}

[
    {
        "att_name": "attribute_gramos",
        "att_value": "15g"
    }
],
[
    {
        "name": "attribute_variedad",
        "value": "var 1"
    }
]
*/