window.AmazonOrder = Class.create(Common, {

    // ---------------------------------------

    resendInvoice: function(orderId, documentType)
    {
        new Ajax.Request(M2ePro.url.get('adminhtml_amazon_order/resendInvoice'), {
            method: 'post',
            parameters: {
                order_id: orderId,
                document_type: documentType
            },
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();

                MessageObj.clearAll();
                MessageObj['add' + response.msg.type[0].toUpperCase() + response.msg.type.slice(1)](response.msg.text);
            }
        });
    },

    // ---------------------------------------

});