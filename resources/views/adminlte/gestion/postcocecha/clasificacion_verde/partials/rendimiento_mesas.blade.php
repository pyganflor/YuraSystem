<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
    var time;
    $(document).ready(function () {
        append_clone();
        pageScroll();
    });

    function append_clone() {
        $("#contain li").each(function () {
            $("#contain li").clone().appendTo("#contain");
        });
    }

    function pageScroll() {
        var objDiv = document.getElementById("contain");
        objDiv.scrollTop = objDiv.scrollTop + 3;
        time = setTimeout('pageScroll()', 100);
        console.log(objDiv.scrollTop);
        if (objDiv.scrollTop >= 6060)
            objDiv.scrollTop = 0;
    }
</script>

<style>
    #contain {
        height: 500px;
        overflow-y: scroll;
    }

    #contain li {
        border-top: 1px solid #ddd;
        padding: 10px 0;
        margin: 0px 0 0 0;
    }
</style>

<ul id="contain">
    <li>1</li>
    <li>2</li>
    <li>3</li>
    <li>4</li>
    <li>5</li>
</ul>