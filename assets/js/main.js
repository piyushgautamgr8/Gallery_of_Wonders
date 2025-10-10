// assets/js/main.js
document.addEventListener('DOMContentLoaded', ()=> {
  // placeholder for later enhancements (AJAX likes, search, etc.)
});

// ---------- Lightbox functionality (with animation) ----------
document.addEventListener("DOMContentLoaded", () => {
  const img = document.getElementById("workImage");
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightboxImg");
  const closeBtn = document.querySelector(".lightbox .close");

  if (img && lightbox && lightboxImg && closeBtn) {
    // Open lightbox
    img.addEventListener("click", () => {
      lightboxImg.src = img.src;
      lightbox.classList.add("show");
      setTimeout(() => (lightbox.style.display = "flex"), 10);
    });

    // Close lightbox
    function closeLightbox() {
      lightbox.classList.remove("show");
      setTimeout(() => (lightbox.style.display = "none"), 300);
    }

    closeBtn.addEventListener("click", closeLightbox);
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) closeLightbox();
    });
  }
});

// ---------- Dashboard Image Preview Lightbox ----------
document.addEventListener("DOMContentLoaded", () => {
  const previewImgs = document.querySelectorAll(".preview-img");
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightboxImg");
  const closeBtn = document.querySelector(".lightbox .close");

  if (previewImgs.length && lightbox && lightboxImg && closeBtn) {
    previewImgs.forEach(img => {
      img.addEventListener("click", () => {
        lightboxImg.src = img.src;
        lightbox.classList.add("show");
        lightbox.style.display = "flex";
      });
    });

    function closeLightbox() {
      lightbox.classList.remove("show");
      setTimeout(() => (lightbox.style.display = "none"), 300);
    }

    closeBtn.addEventListener("click", closeLightbox);
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) closeLightbox();
    });
  }
});


// Like button AJAX
const likeBtn = document.getElementById('likeBtn');
const likeCount = document.getElementById('likeCount');

likeBtn.addEventListener('click', ()=>{
    fetch('like_work.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({work_id:<?= $work_id ?>})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            likeBtn.textContent = data.liked ? '❤️' : '🤍';
            likeBtn.style.color = data.liked ? 'red' : '#555';
            likeCount.textContent = data.total_likes;
        }
    });
});
