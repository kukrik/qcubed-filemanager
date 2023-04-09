$(document).ready(function () {

    $(".control-scrollpad").scrollpad();


    $('[data-control="media-items"]').selectable({
        filter: '[data-type="media-item"]',
        autoRefresh: true,
        stop: function() {
            var items = $('[data-control="media-items"]').find(".ui-selected");
            var result = [];
            for (var i=0, len=items.length; i < len; i++) {
                var item = items[i],
                    itemDetails = {
                        "data-type": item.getAttribute("data-item-type"),
                        "data-path": item.getAttribute("data-path"),
                        "data-title": item.getAttribute("data-title"),
                        "data-size": item.getAttribute("data-size"),
                        "data-mime-type": item.getAttribute("data-mime-type"),
                        "data-dimensions": item.getAttribute("data-dimensions"),
                        "data-last-modified": item.getAttribute("data-last-modified"),
                        "data-document-type": item.getAttribute("data-document-type"),
                        "data-public-url": item.getAttribute("data-public-url")
                    };
                result.push(itemDetails)
            }
            //return result;

            str = JSON.stringify(result);
            alert(str);
            //alert(result.length);


        }
    });






   /* $(".vauu-scroll-table tr").on("click",function() {
        $('tr.ui-selected').removeClass('ui-selected');
        $(this).delay(1000).addClass('ui-selected');
        return true;
    });*/


//$('.vauu-scroll-table tr').click(function() {
//    var href = $(this).find('a').attr('href');
//    if(href) {
//        window.location = href;
//    }
//});

});

