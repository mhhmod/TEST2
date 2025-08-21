// GrindCTRL WP Theme JS: quantity controls and summary only
(function(){
    function qs(id){ return document.getElementById(id); }
    function format(amount, currency){
        try {
            return (parseFloat(amount).toFixed(2)) + ' ' + (currency || '');
        } catch(e){ return amount; }
    }

    function getPrice(){
        if (window.GRINDCTRL_DATA && GRINDCTRL_DATA.product && GRINDCTRL_DATA.product.price){
            return parseFloat(GRINDCTRL_DATA.product.price);
        }
        return 300.00;
    }

    function getCurrency(){
        if (window.GRINDCTRL_DATA && GRINDCTRL_DATA.product && GRINDCTRL_DATA.product.currency){
            return GRINDCTRL_DATA.product.currency;
        }
        return '';
    }

    function updateSummary(){
        var qtyEl = qs('quantity');
        var subtotalEl = qs('subtotal');
        var totalEl = qs('total');
        if (!qtyEl || !subtotalEl || !totalEl) return;
        var q = parseInt(qtyEl.value || '1', 10);
        if (isNaN(q) || q < 1) q = 1;
        var price = getPrice();
        var subtotal = price * q;
        var total = subtotal; // free shipping
        var cur = getCurrency();
        subtotalEl.textContent = format(subtotal, cur);
        totalEl.textContent = format(total, cur);
    }

    function initQty(){
        var dec = qs('decreaseQty');
        var inc = qs('increaseQty');
        var qty = qs('quantity');
        if (!qty) return;
        qty.readOnly = true;
        if (dec) dec.addEventListener('click', function(){ var v = parseInt(qty.value||'1',10); if (v>1){ qty.value = v-1; updateSummary(); }});
        if (inc) inc.addEventListener('click', function(){ var v = parseInt(qty.value||'1',10); if (v<10){ qty.value = v+1; updateSummary(); }});
        qty.addEventListener('change', updateSummary);
    }

    document.addEventListener('DOMContentLoaded', function(){
        initQty();
        updateSummary();
    });
})();

