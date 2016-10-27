function updateVoteStats(like_box, data) {
    var total_like = 0,
        total_dislike = 0;
    if (data.vote_totals !== undefined) {
        total_like = data.vote_totals.like;
        total_dislike = data.vote_totals.dislike;
    }
    like_box.find(".total-dislike").text('-' + total_dislike);
    like_box.find(".total-like").text('+' + total_like);
    like_box.find('.like a').removeClass('g');
    like_box.find('.dislike a').removeClass('r');
    if (data.my_vote !== undefined && data.my_vote) {
        like_box.find("." + data.my_vote).find('a').addClass(data.my_vote == "like" ? "g" : "r");
    }
    Tipped.remove(like_box.find('.cmt-tooltip'));
    var who_voted_like = new Array("voted +1:"),
        who_voted_dislike = new Array("voted -1:");
    if(data.who_voted) {
        $.each(data.who_voted.like, function(i, name) { who_voted_like.push(name); });
        $.each(data.who_voted.dislike, function(i, name) { who_voted_dislike.push(name); });
        if(who_voted_like.length > 10) { 
            who_voted_like.push('<a onclick="showAllVoters(\''+data.target+'\', \'like\', '+data.id+'\)" style="color: blue; text-decoration: underline;">show all voters</a>'); 
        }
        if(who_voted_dislike.length > 10) { 
            who_voted_dislike.push('<a onclick="showAllVoters(\''+data.target+'\', \'dislike\', '+data.id+'\)" style="color: blue; text-decoration: underline;">show all voters</a>'); 
        }
    }
	Tipped.create(like_box.find(".total-dislike"),who_voted_dislike.join('<br/>'),{ containment: 'viewport', position: 'bottom' });
    Tipped.create(like_box.find(".total-like"),who_voted_like.join('<br/>'),{ containment: 'viewport', position: 'bottom' });
}

function showAllVoters(target, type, id) {
    $.ajax({
        url: '/vote/all_voters/' + target,
        type: 'post',
        data: {'type': type, 'id': id},
        dataType: 'json',
        success: function(data) {
            var modal = $('#modal-window');
            modal.find(".modal-hdr-welcme").html('<h2>Voted ' + (type == 'like' ? '+' : '-') + '1</h2>');
            var list = new Array();
            $.each(data.voters, function(i, name) {
                list.push('<li>'+name+'</li>');
            });
            modal.find(".modal-body").html('<ul id="voters-list">'+list.join('')+'</ul>');
            modal.modal('show');
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function vote(obj, target, type, id) {
    $.ajax({
        url: '/vote/add/' + target,
        type: 'get',
        data: {'type': type, 'id': id},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    var like_box = $(obj).closest('[class$="like-box"]');
                    if(!like_box.find('.'+type+' a').hasClass(type == 'like' ? 'g' : 'r')) {
                        data.my_vote = type;
                    }
                    data.target = target;
                    data.id = id;
                    updateVoteStats(like_box, data);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}