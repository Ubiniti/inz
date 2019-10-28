$(document).ready(function () {
    function toggleCommentViewEdit(target) {
        let commentId = $(target).data('comment-id');

        $(target).closest('.comment-content-wrapper').children('.comment-edit').toggle();
        $(target).closest('.comment-content-wrapper').children('.comment-view').toggle();
    }

    $('.btn-edit').on('click', function (e) {
        toggleCommentViewEdit(e.currentTarget);
    });

    $('.btn-accept').on('click', function (e) {
        let commentView = $('.comment-view');
        let commentEdit = $(e.currentTarget).closest('.contents').children('.textarea-reply');
        let editUrl = $(e.currentTarget).data('comment-edit-url');
        let message = $(e.currentTarget).closest('.comment-edit').children('.textarea-reply').first().val();

        console.log(message);
        toggleCommentViewEdit(e.currentTarget);
        // request
    });
});

// toggleCommentViewEdit(e.currentTarget);
// $.ajax({
//     url: editUrl,
//     method : 'post',
//     data : {
//         message: message
//     }
// })
// .done(function (res) {
// });