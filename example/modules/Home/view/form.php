<form method="post" id="sampleForm">
    Name: <input type="text" name="name"><br>
    Age: <input type="text" name="age"><br>
    <input type="submit">
</form>

<br>
<button type="button" id="ajaxCall">Ajax call</button>


<script>
    function ajaxPost() {   
        alert('hehe');
        $.ajax({method: 'POST',
            url: '/form',            
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify($("#sampleForm").serializeJSON()),
            success: function (data) {
                alert("Success!");
            }
        });
        return false;
    }
    
    function setupForm() {
        $("#ajaxCall").on('click', ajaxPost);
    }
</script>

<?php $templater->addFooterContent("<script>$(document).ready(setupForm)</script>");