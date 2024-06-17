function handleInput() {
  var searchBox = document.getElementById('search-box');
  var searchButton = document.getElementById('search-button');

  if (searchBox.value.length > 0) {
    searchButton.classList.add('hidden');
  } else {
    searchButton.classList.remove('hidden');
  }
}

document.getElementById('search-box').addEventListener('input', function() {
  if (this.value.length > 0) {
    this.style.backgroundImage = 'none';
  } else {
    this.style.backgroundImage = "url('assets/images/Shape.png')";
  }
});

document.getElementById('filter-toggle').addEventListener('click', function() {
  var filters = document.getElementById('filters');
  filters.style.display = (filters.style.display === 'none' || !filters.style.display) ? 'block' : 'none';
  this.classList.toggle('rotated');
});

function toggleLike(button, buildId) {
  fetch('/src/actions/like.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ build_id: buildId })
  })
      .then(response => response.json())
      .then(data => {
        if (data.liked !== undefined) {
          button.src = data.liked ? 'assets/images/blacked-up.png' : 'assets/images/Thumb Like.png';
          updateCounts(buildId);
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
}

function toggleUNLike(button, buildId) {
  fetch('/src/actions/dislike.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ build_id: buildId })
  })
      .then(response => response.json())
      .then(data => {
        if (data.disliked !== undefined) {
          button.src = data.disliked ? 'assets/images/blacked-down.png' : 'assets/images/Thumb Like (1).png';
          updateCounts(buildId);
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
}

function updateCounts(buildId) {
  fetch(`/src/actions/get_counts.php?build_id=${buildId}`)
      .then(response => response.json())
      .then(data => {
        if (data.likes !== undefined && data.dislikes !== undefined) {
          document.getElementById(`likes-count-${buildId}`).textContent = data.likes;
          document.getElementById(`dislikes-count-${buildId}`).textContent = data.dislikes;
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



