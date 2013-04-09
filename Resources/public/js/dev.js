//Add HTML5 attribute "novalidate" in <form> markups
// => Disable HTML5 browser validation to leave server validation handle form
window.onload = function ()
{
   $('form').each(function()
         {
            $(this).attr('novalidate', 'novalidate');
         }
   );
}
