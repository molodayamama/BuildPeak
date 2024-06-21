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


// Функция для обработки лайков и дизлайков
function toggleLikeDislike(element, buildId, action) {
  fetch(`/src/actions/${action}.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ build_id: buildId })
  })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log(data);
        const likeButton = document.querySelector(`.likeButton[data-build-id="${buildId}"]`);
        const dislikeButton = document.querySelector(`.unlikeButton[data-build-id="${buildId}"]`);

        if (action === 'like') {
          element.src = data.liked ? 'assets/images/blacked-up.png' : 'assets/images/Thumb Like.png';
          if (data.liked) {
            dislikeButton.src = 'assets/images/Thumb Like (1).png';
          }
        } else if (action === 'dislike') {
          element.src = data.disliked ? 'assets/images/blacked-down.png' : 'assets/images/Thumb Like (1).png';
          if (data.disliked) {
            likeButton.src = 'assets/images/Thumb Like.png';
          }
        }
      })
      .catch(error => console.error('Error:', error));
}

function updateBuildCounts(buildId) {
  fetch(`/src/actions/get_counts.php?build_id=${buildId}`)
      .then(response => response.json())
      .then(data => {
        if (data.likes !== undefined && data.dislikes !== undefined) {
          const likesElement = document.getElementById(`build-likes-count-${buildId}`);
          const dislikesElement = document.getElementById(`build-dislikes-count-${buildId}`);

          if (likesElement) {
            likesElement.textContent = data.likes;
          } else {
            console.error(`Element with id build-likes-count-${buildId} not found.`);
          }

          if (dislikesElement) {
            dislikesElement.textContent = data.dislikes;
          } else {
            console.error(`Element with id build-dislikes-count-${buildId} not found.`);
          }
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

function applyBuildTypeFilter(type) {
  document.getElementById('build-type').value = type;
  document.querySelector('.search-panel').submit();
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

document.getElementById('filter-toggle').addEventListener('click', function() {
  var filters = document.getElementById('filters');
  filters.style.display = (filters.style.display === 'none' || !filters.style.display) ? 'block' : 'none';
  this.classList.toggle('rotated');
});
