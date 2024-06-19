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
        console.log(data); // Добавьте это для отладки
        if (data.favorite !== undefined) {
          const newSrc = data.favorite ? 'assets/images/heart-filled.svg' : 'assets/images/heart.svg';
          element.src = newSrc;
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.commentLikeButton').forEach(element => {
    element.addEventListener('click', () => {
      const commentId = element.dataset.commentId;
      toggleCommentLike(element, commentId);
    });
  });

  document.querySelectorAll('.commentunLikeButton').forEach(element => {
    element.addEventListener('click', () => {
      const commentId = element.dataset.commentId;
      toggleCommentDislike(element, commentId);
    });
  });
});

function toggleCommentLike(element, commentId) {
  fetch('/src/actions/toggle_comment_like.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ comment_id: commentId })
  })
      .then(response => response.json())
      .then(data => {
        if (data.liked !== undefined) {
          element.src = data.liked ? 'assets/images/blacked-up.png' : 'assets/images/Thumb Like.png';
          const dislikeButton = document.querySelector(`.commentunLikeButton[data-comment-id='${commentId}']`);
          if (dislikeButton) {
            dislikeButton.src = 'assets/images/Thumb Like (1).png';
          }
          updateCommentCounts(commentId);
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
}

function toggleCommentDislike(element, commentId) {
  fetch('/src/actions/toggle_comment_dislike.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ comment_id: commentId })
  })
      .then(response => response.json())
      .then(data => {
        if (data.disliked !== undefined) {
          element.src = data.disliked ? 'assets/images/blacked-down.png' : 'assets/images/Thumb Like (1).png';
          const likeButton = document.querySelector(`.commentLikeButton[data-comment-id='${commentId}']`);
          if (likeButton) {
            likeButton.src = 'assets/images/Thumb Like.png';
          }
          updateCommentCounts(commentId);
        } else {
          console.error('Unexpected response:', data);
        }
      })
      .catch(error => console.error('Error:', error));
}

function updateCommentCounts(commentId) {
  fetch(`/src/actions/get_comment_counts.php?comment_id=${commentId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const likeCountElement = document.querySelector(`#like-count-${commentId}`);
          const dislikeCountElement = document.querySelector(`#dislike-count-${commentId}`);

          likeCountElement.textContent = data.likeCount;
          dislikeCountElement.textContent = data.dislikeCount;
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

window.onclick = function (event) {
  var popup = document.getElementById('popup');
  if (!event.target.matches('.avthor-info, .avthor-info *')) {
    popup.style.display = "none";
  }
}

function updateCharCount() {
  const textarea = document.getElementById('desc');
  const charCount = document.getElementById('charCount');
  charCount.textContent = `${textarea.value.length}/300`;
}

function getCommentWord(count) {
  const lastDigit = count % 10;
  const lastTwoDigits = count % 100;

  if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
    return 'комментариев';
  } else if (lastDigit === 1) {
    return 'комментарий';
  } else if (lastDigit >= 2 && lastDigit <= 4) {
    return 'комментария';
  } else {
    return 'комментариев';
  }
}

function updateCharCount() {
  const textarea = document.getElementById('desc');
  const charCount = document.getElementById('charCount');
  charCount.textContent = `${textarea.value.length}/300`;
}

function addComment() {
  const commentInput = document.getElementById('commentInput');
  const commentsList = document.getElementById('commentsList');
  const commentCount = document.getElementById('commentCount');

  const commentText = commentInput.value.trim();
  if (commentText === "") {
    alert("Комментарий не может быть пустым!");
    return;
  }

  const formattedCommentText = formatText(commentText, 65); // Форматируем текст

  const newComment = document.createElement('li');
  newComment.className = 'comment-item';
  const commentHTML = `
  <img src="assets/images/Ellipse 5.png" class="userpic-comment" width="30px">
  <div>
  <strong>Nickname</strong>
  <p>${formattedCommentText}</p>
  <div class="comment-actions">
  <span class="thumb-up"><img src="assets/images/Thumb Like.svg" alt="Like" class="likeButton" onclick="toggleLike(this)" width="25px"></span>
  <span class="thumb-down"><img src="assets/images/Thumb Like (1).svg" class="unlikeButton" onclick="toggleUNLike(this)" width="25px"></span>
  </div>
  </div>
  `;
  newComment.innerHTML = commentHTML;
  commentsList.appendChild(newComment);

  commentInput.value = "";
  adjustTextareaHeight(commentInput); // Сбрасываем высоту текстового поля

  const totalComments = commentsList.children.length;
  commentCount.textContent = totalComments === 0 ? 'Нет комментариев' : `${totalComments} ${getCommentWord(totalComments)}`;
}

function formatText(text, maxLength) {
  const words = text.split(' ');
  let formattedText = '';
  let lineLength = 0;

  words.forEach(word => {
    if (lineLength + word.length > maxLength) {
      formattedText += '\n';
      lineLength = 0;
    }
    formattedText += word + ' ';
    lineLength += word.length + 1;
  });

  return formattedText.trim();
}

function adjustTextareaHeight(textarea) {
  textarea.style.height = 'auto';
  textarea.style.height = textarea.scrollHeight + 'px';
}

document.getElementById('commentInput').addEventListener('input', function () {
  adjustTextareaHeight(this);
});


document.getElementById('buildImage').addEventListener('change', function(event) {
  const [file] = event.target.files;
  if (file) {
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.src = URL.createObjectURL(file);
    imagePreview.style.display = 'block';
  }
});

