<?php

namespace App\Http\Controllers\review;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use Auth;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;

class ReviewController extends Controller
{
    use GeneralTrait;



    public function index(){
        try {
            $review=Review::with('user' ,'product')->get();

            $data=$review;
            $message='these are all review';
            return $this->successResponse($data,$message,201);
        }
        catch (\Exception $ex){


            return $this->errorResponse($ex->getMessage(),500);
        }


    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'comment' => 'required|string',
            'start' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return 'hello';
            return $this->errorResponse($validator->errors(), 422);
        }

        try {

            $review = Review::create($request->all());

            $user = Auth::user();
            $review->user()->associate($user);


            $product = Product::find($request->product_id);

            $review->product()->associate($product);

            $message = 'Done';
            $data = $review;

            return $this->successResponse($data, $message, 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    public function update(Request $request,$id){


        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'comment' => 'required|string',
            'start' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        try {

            $review=Review::find($id);
            if (!$review)
                return $this->errorResponse('can not find review');
            $review->update($request->all());
            $review->save();
            $message= 'updated successfully';
            return $this->successResponse($review,$message,201);

        }
        catch (\Exception $ex){

            return $this->errorResponse($ex->getMessage(),500);

        }

    }

public function delete(Request $request,$id){

    try {
        $review=Review::find($id);
        if (!$review)
            return $this->errorResponse('not found this review',404);

        $review->delete();
        $message='deleted successfully';
        return $this->successResponse($review,$message,201);
    }
    catch (\Exception $ex){
        return $this->errorResponse($ex->getMessage(),500);

    }


}
public function getAllUserReview($user){
    try {
        $review=Review::where('user_id',$user)->get();
        $message='these are all review about this user';
        return $this->successResponse($review,$message,201);
    }
    catch (\Exception $ex){
        return $this->errorResponse($ex->getMessage(),500);
    }

}


public function getAllProductReview($product){
    try {
        $review=Review::where('product_id',$product)->get();
        $message='these are all review about this product';
        return $this->successResponse($review,$message,201);
    }

      catch (\Exception $ex){

        return $this->errorResponse($ex->getMessage(),500);
      }
}


}
