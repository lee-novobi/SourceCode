var options, options_product, objAutoCompleteUser, objAutoCompleteProduct;

$(document).ready(function(){
    options = {
        serviceUrl:'contact/get_list_email_ajax',
        onSelect: function(suggestion) {
            //get_contact_searched_by_user_domain(suggestion.value);
        } 
    };
    objAutoCompleteUser = $('#search_by_user').autocomplete(options);
    
    options_product = {
        serviceUrl: 'contact/get_list_product_ajax',
        onSelect: function(suggestion) {
            //get_contact_searched_by_criteria();
        }
    };
    objAutoCompleteProduct = $('#search_by_product').autocomplete(options_product);
});