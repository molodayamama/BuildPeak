
function toggleLike(element) {
  const card = element.closest('.product-card');
  const unlikeButton = card.querySelector('.unlikeButton');
  var src = element.getAttribute('src');
  var filename = src.split('/').pop(); 

  if (filename === 'Thumb Like.svg') {
    element.src = 'assets/images/blacked-up.png';
 
    unlikeButton.src = 'assets/images/Thumb Like (1).svg';
  } else {
    element.src = 'assets/images/Thumb Like.svg';
  }
}

function toggleUNLike(element) {
  const card = element.closest('.product-card');
  const likeButton = card.querySelector('.likeButton');
  var src = element.getAttribute('src');
  var filename = src.split('/').pop(); 

  if (filename === 'Thumb Like (1).svg') {
    element.src = 'assets/images/blacked-down.png';

    likeButton.src = 'assets/images/Thumb Like.svg';
  } else {
    element.src = 'assets/images/Thumb Like (1).svg';
  }
}

function toggleHeart(element) {
  const defaultHeart = 'assets/images/heart.svg';
  const activeHeart = 'assets/images/heart-filled.svg';

  element.src = element.src.includes(defaultHeart) ? activeHeart : defaultHeart;
}

function togglePopup(element) {
  var popup = document.getElementById('popup');
  if (popup.style.display === 'none') {
      var rect = element.getBoundingClientRect();
      popup.style.top = rect.bottom + 'px'; 
      popup.style.left = rect.left + 'px'; 
      popup.style.display = 'block';
  } else {
      popup.style.display = 'none'; 
  }
}

window.onclick = function(event) {
  var popup = document.getElementById('popup');
  if (!event.target.matches('.avthor-info, .avthor-info *')) {
      popup.style.display = "none";
  }
}

document.getElementById('buildImage').addEventListener('change', function(event) {
  const [file] = event.target.files;
  if (file) {
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.src = URL.createObjectURL(file);
    imagePreview.style.display = 'block';
  }
});

function updateCharCount() {
  const textarea = document.getElementById('desc');
  const charCount = document.getElementById('charCount');
  charCount.textContent = `${textarea.value.length}/300`;
}


function toggleCheckmark(button) {
  const buttons = document.querySelectorAll('button[id^="gal"]');

  if (button.classList.contains('checkmark')) {
    button.classList.remove('checkmark');
    button.classList.remove('background-checked');
  } else {
    buttons.forEach(btn => {
      btn.classList.remove('checkmark');
      btn.classList.remove('background-checked');
    });
    button.classList.add('checkmark');
    button.classList.add('background-checked');
  }
}
