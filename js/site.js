// 'WELCOME TO HELL!!!'
//posting message
$(function(){
    $('.post').submit(function (){
      var cond1 = $('[name="name"]').val();
      var cond2 = $('[name="message"]').val();
      if ( cond1 && cond2 ) {
        $.ajax({
            method: "POST",
            url: "index.php?ajax=1",
            data: { name: $('[name="name"]').val(), message: $('[name="message"]').val() }
          }).done(function( msg ) {
              $(".comments").html(msg);
            });
          $('[name="name"]').val('');
          $('[name="message"]').val('');
          console.log('success');
          return false;
      } else {
          console.log('nothing to post');
          return false;
      }
})
})

// deleting message from database
$(function(){
    $(document).on('submit', '.delete', function (){
    $(this).parents('.pcom').remove();
    var pid = $(this).find('[name="id"]').val();
    $.ajax({
      method: "POST",
      url: "index.php",
      data: { id: pid }
    }).done();
    return false;
})
})

// search a message
$(function(){
    $('.search').submit(function (){
      var cond = $('[name="keyword"]').val()
      if ( cond ) {
        $.ajax({
          method: "POST",
          url: "index.php?jss=1",
          data: { keyword: $('[name="keyword"]').val() }
        }).done(function( msg ) {
            // console.log(msg);
            $(".comments").html(msg);
          });
        $('[name="keyword"]').val('');
        return false;
        console.log('search');
      } else {
        console.log('empty keyword field!!!');
        return false;
      }
})
})

// giving name for page)))
function titleq () {
  return 'The Guest Book';
}

// i declared about that already
document.getElementById('pagename').innerHTML = titleq();


// just empty function for your brainfuck....
function updatepageinfor () {
  return false;
}
