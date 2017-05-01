var active = false;
var lastmenu;

function getId(id)
{
    if (document.getElementById){  
        return document.getElementById(id);
    } else if (document.all) {
        return document.all.id;
    } else {
        return document.id;
    }
}
  
function hover(elem, id)
{
    elem.style.backgroundColor='#6699CC';
    if (active) {
        expand(id);
    }
}

function unhover(elem)
{
    elem.style.backgroundColor='#EEEEEE';
}

function expand(id)
{
    elem = getId(id);
    if (elem.style.visibility == 'visible') {
        active = false;
        elem.style.visibility = 'hidden';
        elemImg = getId(id + 'img');
        elemImg.src = 'images/right.gif';
    } else {
        active = true;
        hideAll();
        elem.style.visibility = 'visible';
        elemImg = getId(id + 'img');
        elemImg.src = 'images/down.gif';
        lastmenu = elem;
    }        
}

function hideAll()
{
    if (lastmenu) {
        lastmenu.style.visibility = 'hidden';
    }
}

$(document).ready(function menuReady() {
$('.items a').on('click', function() {
  var $this = $(this),
      $bc = $('<div class="item"></div>');

  $this.parents('li').each(function(n, li) {
      var $a = $(li).children('a').clone();
      $bc.prepend(' / ', $a);
  });
    $('.breadcrumb').html( $bc.prepend('<a href="#home">Home</a>') );
    return false;
}) 
});
  