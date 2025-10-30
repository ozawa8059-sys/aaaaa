document.addEventListener('DOMContentLoaded', function(){

  // === Buy Modal ===
  const modal = document.getElementById('buyModal');
  const closeBtn = document.querySelector('.modal-close');

  document.querySelectorAll('.buy-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
      const title = this.dataset.title || 'Product';
      document.getElementById('modalTitle').textContent = 'Buy: ' + title;
      modal.style.display = 'flex';
    });
  });

  closeBtn.addEventListener('click', ()=> modal.style.display = 'none');
  window.addEventListener('click', e => { if(e.target===modal) modal.style.display='none'; });

  // === Card Slider (Thumbnail) ===
  document.querySelectorAll('.image-slider').forEach(slider => {
    const slides = slider.querySelectorAll('.slide');
    const prevBtn = slider.querySelector('.prev');
    const nextBtn = slider.querySelector('.next');
    let index = 0;

    const showSlide = i => slides.forEach((s,j)=>s.classList.toggle('active', j===i));
    showSlide(0); // show first image only in thumbnail

    if(prevBtn) prevBtn.addEventListener('click', ()=> { 
      index=(index-1+slides.length)%slides.length; 
      showSlide(index); 
    });
    if(nextBtn) nextBtn.addEventListener('click', ()=> { 
      index=(index+1)%slides.length; 
      showSlide(index); 
    });

    // click on thumbnail to open lightbox
    slider.addEventListener('click', e=>{
      if(e.target.tagName!=='IMG') return;
      const images = JSON.parse(slider.dataset.images);
      openLightbox(images);
    });
  });

  // === Lightbox ===
  const lightbox = document.createElement('div');
  lightbox.className='lightbox';
  lightbox.innerHTML=`
    <button class="lightbox-close">&times;</button>
    <div class="lightbox-content"></div>
    <button class="prev">‹</button>
    <button class="next">›</button>
  `;
  document.body.appendChild(lightbox);

  const lightboxContent = lightbox.querySelector('.lightbox-content');
  const lbPrev = lightbox.querySelector('button.prev');
  const lbNext = lightbox.querySelector('button.next');
  const lbClose = lightbox.querySelector('.lightbox-close');
  let lbIndex = 0, lbSlides=[];

function openLightbox(images){
  lightboxContent.innerHTML = '';
  lbSlides = images.map((src, i) => {
    const slide = document.createElement('div');
    slide.className = 'lightbox-slide ' + (i === 0 ? 'active' : '');
    const img = document.createElement('img');
    img.src = '/uploads/' + src;
    slide.appendChild(img);
    lightboxContent.appendChild(slide);
    return slide;
  });
  lbIndex = 0;
  lightbox.classList.add('active');
}

  function showLbSlide(i){ 
    lbSlides.forEach((s,j)=>s.classList.toggle('active', j===i)); 
    resizeLightbox();
  }

  lbPrev.addEventListener('click', ()=> { 
    lbIndex=(lbIndex-1+lbSlides.length)%lbSlides.length; 
    showLbSlide(lbIndex); 
  });
  lbNext.addEventListener('click', ()=> { 
    lbIndex=(lbIndex+1)%lbSlides.length; 
    showLbSlide(lbIndex); 
  });

  lbClose.addEventListener('click', ()=> lightbox.classList.remove('active'));
  lightbox.addEventListener('click', e => { if(e.target===lightbox) lightbox.classList.remove('active'); });

  // Resize handling for responsiveness
  window.addEventListener('resize', resizeLightbox);
  function resizeLightbox(){
    lbSlides.forEach(slide=>{
      const img = slide.querySelector('img');
      if(!img) return;
      const maxWidth = window.innerWidth * 0.95;
      const maxHeight = window.innerHeight * 0.95;
      img.style.maxWidth = maxWidth + 'px';
      img.style.maxHeight = maxHeight + 'px';
    });
  }

});
