/**
 * Follow button
 *
 * This part add an event on every follow/unfollow button and
 * change it on click by fetching the server.
 */
let followButtons = document.querySelectorAll('.follow-button');

followButtons.forEach((btn) => {
    btn.addEventListener('click', ({ currentTarget: button }) => {
        const idUser = button.value;

        fetch(PATH_FOLLOW, {
            headers: { 'Content-Type': 'application/json; charset=utf-8' },
            method: 'POST',
            body: JSON.stringify({
                idFollow: idUser,
            }),
        })
            .then((resp) => {
                if (resp.status === 200) {
                    resp.json().then((isFollow) => {
                        let anim = button.animate([
                            { transform: 'translateZ(0) rotateX(0deg)' },
                            { transform: 'translateZ(200px) rotateX(-90deg)' },
                        ], {
                            duration: 500,
                            iterations: 1,
                            direction: 'alternate',
                            fill: 'forwards',
                        });

                        anim.onfinish = () => {
                            if (isFollow) {
                                button.innerHTML = 'Unfollow';
                            } else {
                                button.innerHTML = 'Follow';
                            }

                            button.animate([
                                { transform: 'translateZ(200px) rotateX(90deg)' },
                                { transform: 'translateZ(0) rotateX(0deg)' },
                            ],
                            {
                                duration: 500,
                                iterations: 1,
                                direction: 'alternate',
                                fill: 'forwards',
                            });
                        };
                    });
                } else {
                    resp.json().then((errorLog) => {
                        console.log(errorLog);
                    });
                }
            });
    });
});

let ddmButtons = document.querySelectorAll('.ddm-button');

ddmButtons.forEach((btn) => {
    btn.addEventListener('click', ({ currentTarget: button }) => {
        button.parentNode.querySelector('ul').classList.toggle('ddm-show');
    });
});