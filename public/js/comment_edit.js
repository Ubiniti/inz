$(document).ready(function () {
    function toggleCommentViewEdit(target) {
        $(target).closest('.comment-content-wrapper').children('.comment-edit').toggleClass('d-none');
        $(target).closest('.comment-content-wrapper').children('.comment-view').toggleClass('d-none');
    }

    $('.btn-edit').on('click', function (e) {
        toggleCommentViewEdit(e.currentTarget);
    });

    $('.btn-accept').on('click', function (e) {
        let commentText = $(e.currentTarget).closest('.comment-content-wrapper').find('.comment-view .comment-text');
        let editUrl = $(e.currentTarget).data('edit-url');
        let message = $(e.currentTarget).closest('.comment-edit').children('.textarea-reply').first().val();

        toggleCommentViewEdit(e.currentTarget);

        $.ajax({
            url: editUrl,
            method : 'post',
            data : {
                message: message
            }
        })
        .done(function (comment) {
            commentText.html(comment.contents.replace('\n', '<br>'));
        });
    });
});