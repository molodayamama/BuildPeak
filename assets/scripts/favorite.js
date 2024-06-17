
function toggleHeart(element, buildId) {
  fetch('/src/actions/toggle_favorite.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ build_id: buildId })
  })
      .then(response => response.json())
      .then(data => {
        if (data.favorite !== undefined) {
          element.src = data.favorite ? 'assets/images/heart-filled.svg' : 'assets/images/heart.svg';
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
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


