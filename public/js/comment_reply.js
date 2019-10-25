 $(document).ready(function () {
     $('.btn-reply').on('click', function (e) {
         e.preventDefault();
         let commentId = $(e.currentTarget).data('comment-id');
         let replyForm = $(`.subcomment-reply[data-comment-id='${commentId}']`);
         replyForm.toggleClass('d-none');
         replyForm.toggleClass('d-flex');
     });
});