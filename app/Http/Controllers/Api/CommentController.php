<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Comment;
use App\Reply;
use App\Reaction;
use App\Notification;
use App\Tag;
class CommentController extends Controller
{
    //
    
    
    public function singleComment($id){
        $comments=$this->comments($id,"comment");
        if(!isset($comments[0]))
            return response()->json(["status"=>400,'message'=>'Comment Not Found']);
            
        return response()->json(["status"=>200,'message'=>'Comment Data','data'=>$comments[0]]);
        
    }

    public function comments($id,$id_type="profile")
    {
		$loggedUser=$this->authUser();
		if($id_type==="profile")
		    $comments=Comment::with("replies","reactions")->where('profile_id',$id)->orderBy('id', 'DESC')->get();
		else
		    $comments=Comment::with("replies","reactions")->where('id',$id)->get();
		$userIDs=[];
		foreach($comments as $comment)
		{
		    
		    $tags=[]; $ids=[]; $tagsData=[];
		    if($comment->tags)
		    {
				$tags=explode(",",$comment->tags);
				for($i=0;$i<sizeof($tags);$i++)
				    $tagsData[$i]['name']=$tags[$i];
		    }
			if($comment->ids)
			{
				$ids=explode(",",$comment->ids);
				for($i=0;$i<sizeof($ids);$i++)
				    $tagsData[$i]['id']=$ids[$i];
			}
			
			$comment->tagsData=$tagsData;
                
			if(!in_array($comment->profile_id,$userIDs))
				$userIDs[]=$comment->profile_id;
			if(!in_array($comment->commenter_id,$userIDs))
				$userIDs[]=$comment->commenter_id;
			foreach($comment->replies as $reply)
			{
				$tags=[]; $tagsData=[];
		    if($reply->tags)
				$tags=explode(",",$reply->tags);
			if($reply->ids)
				$ids=explode(",",$reply->ids);
			for($i=0;$i<sizeof($tags);$i++)
			{
				$tagsData[$i]['name']=$tags[$i];
				$tagsData[$i]['id']=$ids[$i];
			}
			$reply->tagsData=$tagsData;
				
				if(!in_array($reply->replier_id,$userIDs))
				$userIDs[]=$reply->replier_id;
			}
			foreach($comment->reactions as $reaction)
			{
				if(!in_array($reaction->reactor_id,$userIDs))
				$userIDs[]=$reaction->reactor_id;
			}
		}
		
		$users = DB::table('users')
            ->select('name','photo','id')
			->whereIn('id',$userIDs)
			->orderBy('id', 'DESC')
            ->get();
		$userIDs=[];
		foreach($users as $user)
				$userIDs[]=$user->id;
		
		foreach($comments as $comment)
		{
			$index=array_search($comment->profile_id,$userIDs);
			$comment->name=$users[$index]->name;
			$comment->photo=$users[$index]->photo;

			$index=array_search($comment->commenter_id,$userIDs);
			$comment->commenter_name=$users[$index]->name;
			$comment->commenter_photo=$users[$index]->photo;
			$comment->my_reaction=null;
			$comment->total_replies=sizeof($comment->replies);
			$comment->total_reactions=sizeof($comment->reactions);
			
			foreach($comment->replies as $reply)
			{
				$index=array_search($reply->replier_id,$userIDs);
				$reply->replier_name=$users[$index]->name;
				$reply->replier_photo=$users[$index]->photo;
			}
			foreach($comment->reactions as $reaction)
			{
				if($reaction->reactor_id==$loggedUser->id)
					$comment->my_reaction=$reaction->reaction;
				$index=array_search($reaction->reactor_id,$userIDs);
				$reaction->reactor_name=$users[$index]->name;
				$reaction->reactor_photo=$users[$index]->photo;
			}
		}
		
		if($id_type!=="profile")
		    return $comments;
		    
        return response()->json(["status"=>200,'message'=>'Comments Data','data'=>$comments]);
    }
	
	public function comment(Request $request){
		$rules=[
            'comment' => ['max:255','min:1'],
			'attachment' => 'mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff,mp4,wav,webm,avi,3gp,mov,flv,ogg|max:30720',
			 'profile_id' => ['required'],
			 'tags' => ['max:191'],
			 'ids' => ['max:191']
            ];
		$this->validateInput($rules,$request);
			
		if($request->tags && $request->ids)
		{
		    if(sizeof(explode(',',$request->tags)) != sizeof(explode(',',$request->ids)))
		        return response()->json(["status"=>400,"message" =>"Ooops error while adding tags , size not matched"]);
		}
		
        $user=$this->authUser();
        $comment=new Comment();
        
        $comment->comment=$request->comment;
		$comment->profile_id=$request->profile_id;
		$comment->commenter_id=$user->id;
		$comment->tags=$request->tags;
		$comment->ids=$request->ids;
		if($request->attachment)
		{
			$attachment = time().rand(999,9999).'.'.request()->attachment->getClientOriginalExtension();
			if(request()->attachment->move('images/comments', $attachment))
				$comment->attachment="images/comments/".$attachment;
		}
        if($comment->save())
        {
             $notification=new NotificationController();
            $tags=[]; $tagsData=[];
		     if($comment->tags)
                $tags=explode(",",$comment->tags);
             if($comment->ids)
             {
                $subject=$user->name;
			    $body="has tagged you in a comment";
			    $ids=explode(",",$comment->ids);
				foreach($ids as $id)
				{
				    $notify=new Notification();
				    $notify->user_id=$id;
				    $notify->sender_id=$user->id;
				    $notify->contentID=$comment->profile_id;
				    $notify->subject=$subject;
				    $notify->body=$body;
				    $notify->type="tag";
				    $notify->save();
				}
                $notification->sendToMultiple($ids,$subject,$body,$user->id,$comment->id,'tag');
             }
			for($i=0;$i<sizeof($tags);$i++)
			{
				$tagsData[$i]['name']=$tags[$i];
				$tagsData[$i]['id']=$ids[$i];
			}
			$comment->tagsData=$tagsData;
			
		    $profile=User::find($request->profile_id);
            $notification->sendToSingle($profile->id,$user->name,"has commented on your profile",$user->id,$comment->id,'comment');
            return response()->json(["status"=>200,"message" =>"Comment Added Successfully",'data'=>$comment]);
        }
        return response()->json(["status"=>400,"message" =>"Ooops  Comment Could Not be added"]);
    }
	
	public function editComment(Request $request){
		$rules=[
            'comment' => ['required','max:255','min:1'],
			'attachment' => 'max:20480',
			 'comment_id' => ['required',"min:0"],
			  'tags' => ['max:191']
            ];
			
		$this->validateInput($rules,$request);
		
		if($request->tags && $request->ids)
		{
		    if(sizeof(explode(',',$request->tags)) != sizeof(explode(',',$request->ids)))
		        return response()->json(["status"=>400,"message" =>"Ooops error while adding tags"]);
		}
		
		$comment=Comment::find($request->comment_id);
		if($request->comment)
			$comment->comment=$request->comment;
		if($request->tags && $request->ids)
		{
		    	$comment->tags=$request->tags;
		    	$comment->ids=$request->ids;
		}
		if($request->attachment && $request->attachment!="removed")
		{
			$attachment = time().rand(999,9999).'.'.request()->attachment->getClientOriginalExtension();
			if(request()->attachment->move('images/comments', $attachment))
			{
				$oldAttachment=$comment->attachment;
				$comment->attachment="images/comments/".$attachment;
				if(file_exists($oldAttachment))
					unlink($oldAttachment);
			}
		}
		elseif($request->action=="removed")
		{
			if($comment->attachment && file_exists($comment->attachment))
					unlink($comment->attachment);
			$comment->attachment=null;
		}
		if($comment->save())
		{
		    $tags=[]; $tagsData=[];
		     if($comment->tags)
                $tags=explode(",",$comment->tags);
             if($comment->ids)
                $ids=explode(",",$comment->ids);
			for($i=0;$i<sizeof($tags);$i++)
			{
				$tagsData[$i]['name']=$tags[$i];
				$tagsData[$i]['id']=$ids[$i];
			}
			$comment->tagsData=$tagsData;
                
			return response()->json(["status"=>200,"message" =>"Comment Updated Successfully",'data'=>$comment]);
		}
        return response()->json(["status"=>400,"message" =>"Ooops  Comment Could Not be Updated"]);		
	}
	
	public function reply(Request $request){
	    
		$rules=[
            'reply' => ['required','max:255','min:1'],
			 'comment_id' => ['required'],
			 'tags' => ['max:191'],
			 'ids' => ['max:191']
            ];
			
		$this->validateInput($rules,$request);
		
		if($request->tags && $request->ids)
		{
		    if(sizeof(explode(',',$request->tags)) != sizeof(explode(',',$request->ids)))
		        return response()->json(["status"=>400,"message" =>"Ooops error while adding tags"]);
		}
		
		
			
        $user=$this->authUser();
        $reply=new Reply();
        
        $reply->reply=$request->reply;
		$reply->comment_id=$request->comment_id;
		$reply->replier_id=$user->id;
		$reply->tags=$request->tags;
		$reply->ids=$request->ids;
	
        if($reply->save())
        {
            $notification=new NotificationController();
            $tags=[]; $tagsData=[];
		    if($reply->tags)
				$tags=explode(",",$reply->tags);
			if($reply->ids)
			{
			    $comment=Comment::find($request->comment_id);
			    $subject=$user->name;
			    $body="has tagged you in a reply";
				$ids=explode(",",$reply->ids);
				foreach($ids as $id)
				{
				    $notify=new Notification();
				    $notify->user_id=$id;
				    $notify->sender_id=$user->id;
				   $notify->contentID=$comment->profile_id;
				    $notify->subject=$subject;
				    $notify->body=$body;
				    $notify->type="tag";
				    $notify->save();
				}
				$notification->sendToMultiple($ids,$subject,$body,$user->id,$reply->id,'tag');
			}
			
			for($i=0;$i<sizeof($tags);$i++)
			{
				$tagsData[$i]['name']=$tags[$i];
				$tagsData[$i]['id']=$ids[$i];
			}
			$reply->tagsData=$tagsData;
                
            $comment=Comment::find($request->comment_id);
		    $profile=User::find($comment->profile_id);
            $notification->sendToSingle($profile->id,$user->name,"has replied to your comment",$user->id,$reply->id,'reply');
            return response()->json(["status"=>200,"message" =>"Reply added successfully"]);
        }
        return response()->json(["status"=>400,"message" =>"Ooops  Reply Could Not be added"]);
    }
    
    
    
    public function editReply(Request $request){
		$rules=[
            'reply' => ['required','max:255'],
			'attachment' => 'max:20480',
			 'reply_id' => ['required',"min:0"],
			  'tags' => ['max:191']
            ];
			
		$this->validateInput($rules,$request);
		
		
		if($request->tags && $request->ids)
		{
		    if(sizeof(explode(',',$request->tags)) != sizeof(explode(',',$request->ids)))
		        return response()->json(["status"=>400,"message" =>"Ooops error while adding tags"]);
		}
		
		
		$reply=Reply::find($request->reply_id);
		if(!$reply)
			 return response()->json(["status"=>400,"message" =>"Ooops Reply Not found"]);
		if($request->reply)
			$reply->reply=$request->reply;
		if($request->tags && $request->ids)
		{
		    $reply->tags=$request->tags;
		    $reply->ids=$request->ids;
		}
		if($request->attachment && $request->attachment!="removed")
		{
			$attachment = time().rand(999,9999).'.'.request()->attachment->getClientOriginalExtension();
			if(request()->attachment->move('images/comments', $attachment))
			{
				$oldAttachment=$comment->attachment;
				$reply->attachment="images/comments/".$attachment;
				if(file_exists($oldAttachment))
					unlink($oldAttachment);
			}
		}
		elseif($request->action=="removed")
		{
			if($reply->attachment && file_exists($reply->attachment))
					unlink($reply->attachment);
			$reply->attachment=null;
		}
		if($reply->save())
		{
		    $tags=[]; $tagsData=[];
		     if($reply->tags)
                $tags=explode(",",$reply->tags);
             if($reply->ids)
                $ids=explode(",",$reply->ids);
			for($i=0;$i<sizeof($tags);$i++)
			{
				$tagsData[$i]['name']=$tags[$i];
				$tagsData[$i]['id']=$ids[$i];
			}
			$reply->tagsData=$tagsData;
                
			return response()->json(["status"=>200,"message" =>"Reply Updated Successfully",'data'=>$reply]);
		}
        return response()->json(["status"=>400,"message" =>"Ooops  Reply Could Not be Updated"]);		
	}
	
    public function reaction($commentID,$react){
		$user=$this->authUser();
		$reaction=Reaction::where('comment_id',$commentID)->where("reactor_id",$user->id)->first();
		if($reaction)
		{
			if($reaction->reaction==$react)
			{
				$reaction->delete();
				$reaction->reaction=0;
				return response()->json(["status"=>200,"message" =>"Reaction Removed Successfully",'data'=>$reaction]);
			}
			else
				$reaction->reaction=$react;
		}
		else
		{
			$reaction=new Reaction();
			$reaction->comment_id=$commentID;
			$reaction->reactor_id=$user->id;
			$reaction->reaction=$react;
		}
		
		if($reaction->save())
		{
		      $comment=Comment::find($commentID);
		      $profile=User::find($comment->profile_id);
    		  $notification=new NotificationController();
              $notification->sendToSingle($profile->id,"Reaction On Your Comment",$user->name." has Reacted To Your Comment",$user->id,$reaction->id,'reaction');
              
             return response()->json(["status"=>200,"message" =>"Reaction Added Successfully",'data'=>$reaction]);
		}
		
		/*$reaction = Reaction::updateOrCreate(['comment_id' => $commentID,'reactor_id'=>$user->id], [ 
			'reaction' => $react
		]);*/
        
        return response()->json(["status"=>400,"message" =>"Ooops  Reaction Could Not be added"]);	
	}
	
	public function destroy($id)
	{
		if($comment=Comment::find($id))
		{
			if($comment->delete())
				return response()->json(["status"=>200,"message" =>"Comment Deleted Successfully"]);
			return response()->json(["status"=>400,"message" =>"Comment Could Not be Deleted"]);
		}
		return response()->json(["status"=>400,"message" =>"Comment Not Found"]);

	}
	
	public function deleteReply($id)
	{
		if($reply=Reply::find($id))
		{
			if($reply->delete())
				return response()->json(["status"=>200,"message" =>"Reply Deleted Successfully"]);
			return response()->json(["status"=>400,"message" =>"Reply Could Not be Deleted"]);
		}
		return response()->json(["status"=>400,"message" =>"Reply Not Found"]);

	}
	
	
	
	
	public function returnComments($id,$limit=50)
    {
		$loggedUser=$this->authUser();
		$comments=Comment::with("replies","reactions")->where('profile_id',$id)->orderBy('id', 'DESC')->take($limit)->get();
		$userIDs=[];
		foreach($comments as $comment)
		{
			$tags=[]; $ids=[]; $tagsData=[];
		     if($comment->tags)
		     {
                $tags=explode(",",$comment->tags);
                for($i=0;$i<sizeof($tags);$i++)
				    $tagsData[$i]['name']=$tags[$i];
		     }
             if($comment->ids)
             {
                $ids=explode(",",$comment->ids);
                for($i=0;$i<sizeof($ids);$i++)
				$tagsData[$i]['id']=$ids[$i];
             }
				
			$comment->tagsData=$tagsData;
                
			if(!in_array($comment->profile_id,$userIDs))
				$userIDs[]=$comment->profile_id;
			if(!in_array($comment->commenter_id,$userIDs))
				$userIDs[]=$comment->commenter_id;
			foreach($comment->replies as $reply)
			{
				
			$tags=[]; $ids=[]; $tagsData=[];
		    if($reply->tags)
		    {
				$tags=explode(",",$reply->tags);
				for($i=0;$i<sizeof($tags);$i++)
				    $tagsData[$i]['name']=$tags[$i];
		    }
			if($reply->ids)
			{
				$ids=explode(",",$reply->ids);
				for($i=0;$i<sizeof($ids);$i++)
				    $tagsData[$i]['id']=$ids[$i];
			}
		
			$reply->tagsData=$tagsData;
			
				if(!in_array($reply->replier_id,$userIDs))
				$userIDs[]=$reply->replier_id;
			}
			foreach($comment->reactions as $reaction)
			{
				if(!in_array($reaction->reactor_id,$userIDs))
				$userIDs[]=$reaction->reactor_id;
			}
		}
		
		$users = DB::table('users')
            ->select('name','photo','id')
			->whereIn('id',$userIDs)
			->orderBy('id', 'DESC')
            ->get();
		$userIDs=[];
		foreach($users as $user)
				$userIDs[]=$user->id;
		
		foreach($comments as $comment)
		{
			$index=array_search($comment->profile_id,$userIDs);
			$comment->name=$users[$index]->name;
			$comment->photo=$users[$index]->photo;

			$index=array_search($comment->commenter_id,$userIDs);
			$comment->commenter_name=$users[$index]->name;
			$comment->commenter_photo=$users[$index]->photo;
			$comment->my_reaction=null;
			$comment->total_replies=sizeof($comment->replies);
			$comment->total_reactions=sizeof($comment->reactions);
			
			foreach($comment->replies as $reply)
			{
				$index=array_search($reply->replier_id,$userIDs);
				$reply->replier_name=$users[$index]->name;
				$reply->replier_photo=$users[$index]->photo;
			}
			foreach($comment->reactions as $reaction)
			{
				if($reaction->reactor_id==$loggedUser->id)
					$comment->my_reaction=$reaction->reaction;
				$index=array_search($reaction->reactor_id,$userIDs);
				$reaction->reactor_name=$users[$index]->name;
				$reaction->reactor_photo=$users[$index]->photo;
			}
		}
		
        return $comments;
    }
	
	
	public function tag(Request $request){
		$rules=[
            'tags' => ['required','max:255','min:1']
            ];
		$this->validateInput($rules,$request);
		if(!$request->reply_id && !$request->comment_id)
			return response()->json(["status"=>400,"message" =>"Both IDs cannot be empty"]);
			
        $user=$this->authUser();
		$tag=new Tag();
		$tag->tags=$request->tags;
		$tag->reply_id=$request->reply_id;
		$tag->comment_id=$request->comment_id;
		if($tag->save())
			return response()->json(["status"=>200,"message" =>"Tags Added Successfully",'data'=>$tag]);
		return response()->json(["status"=>400,"message" =>"Tags Could Not be Added"]);
	}

}
