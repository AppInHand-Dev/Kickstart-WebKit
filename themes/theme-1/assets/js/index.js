/*!
 * v1.0.0 28/05/2026
 * Author: App In Hand
 */

// themes/theme-1/assets/js/ui.js
document.addEventListener('DOMContentLoaded', function(){
  // simple hover lift for nav links on touch devices fallback
  document.querySelectorAll('.nav.nav-1 a').forEach(function(el){
    el.addEventListener('touchstart', function(){ el.classList.add('touched'); });
    el.addEventListener('touchend', function(){ setTimeout(()=>el.classList.remove('touched'),200); });
  });

  document.querySelectorAll('.link.link-1').forEach(function(a){
    a.addEventListener('touchstart', function(){ this.classList.add('touched'); });
    a.addEventListener('touchend', function(){ setTimeout(()=>this.classList.remove('touched'),200); });
  });

  document.querySelectorAll('.btn-copy').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var value = btn.getAttribute('data-copy');
      if (!value) return;
      navigator.clipboard?.writeText(value).then(function(){
        // feedback visivo semplice
        var old = btn.innerHTML;
        btn.innerHTML = '✅';
        setTimeout(function(){ btn.innerHTML = old; }, 1200);
      }).catch(function(){
        // fallback: seleziona testo se clipboard non disponibile
        var temp = document.createElement('input');
        temp.value = value;
        document.body.appendChild(temp);
        temp.select();
        try { document.execCommand('copy'); }
        catch(e){}
        document.body.removeChild(temp);
      });
    });
  });

  document.getElementById('year').textContent = new Date().getFullYear();
});