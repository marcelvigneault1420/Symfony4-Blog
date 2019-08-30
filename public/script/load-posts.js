let lockChange = false;
let idCurrentFollower = 0;

let filterButtons = document.querySelectorAll('.follower-button');
let sortingDropDown = document.querySelector('#order-by');

function clearAllButtonsSelected() {
    document.querySelectorAll('.button-selected').forEach((btn) => {
        btn.classList.remove('button-selected');
    });
}

function loadingWithAnimations(articlesContainer) {
    let anim = articlesContainer.animate([
        { opacity: 1, transform: 'translateY(0)' },
        { opacity: 0, transform: 'translateY(100%)' },
    ], {
        duration: 600,
        iterations: 1,
        direction: 'alternate',
        easing: 'linear',
        fill: 'none',
    });

    anim.onfinish = () => {
        articlesContainer.innerHTML = '<div class="loading-wrapper animate-in"><div class="loading"></div><div class="loading-text">Loading...</div></div>';
    };

    return anim;
}

function drawPosts(anim, articlesContainer, htmlview) {
    anim.cancel();
    if (htmlview !== null && htmlview !== undefined) {
        articlesContainer.innerHTML = htmlview;
    } else {
        articlesContainer.innerHTML = '<article><p>No post found</p></article>';
    }
    lockChange = false;
}

function refreshFollower() {
    let articlesContainer = document.querySelector('.posts');
    let anim = loadingWithAnimations(articlesContainer);

    const url = `${PATH_POSTS}?id=${idCurrentFollower}`;
    fetch(url).then((resp) => {
        if (resp.status === 200) {
            resp.json().then(({ htmlview }) => {
                drawPosts(anim, articlesContainer, htmlview.split('<article>').join('<article class="animate-in">'));
            }, (error) => {
                drawPosts(anim, articlesContainer);
            });
        } else {
            drawPosts(anim, articlesContainer);
        }
    }, (error) => {
        drawPosts(anim, articlesContainer);
    });
}

filterButtons.forEach((btn) => {
    btn.addEventListener('click', ({ currentTarget: button }) => {
        if (!button.classList.contains('button-selected')) {
            if (!lockChange) {
                lockChange = true;
                clearAllButtonsSelected();
                button.classList.add('button-selected');

                idCurrentFollower = button.value;

                refreshFollower();
            }
        }
    });
});