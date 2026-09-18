<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\library\LibraryBook;
use App\Models\library\LibraryCategory;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use File;
use Session;
use Hash;
use Str;
use Redirect;
use PDF;
use Helper;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\YourExcelImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LibraryBookController extends Controller

{

    public function bookAddExcel(Request $request){
        $the_file = $request->file('excel');
        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'F', $column_limit );
            $startcount = 2;
            $data = array();
          
            foreach ( $row_range as $row ) {
              
               
                $data[] = [
                    'user_id' => Session::get('id'),
                    'branch_id' => Session::get('branch_id'),
                    'session_id' => Session::get('session_id'),
                    'category_id' => $sheet->getCell( 'A' . $row )->getValue(),
                    'name' => $sheet->getCell( 'B' . $row )->getValue(),
                    'author' => $sheet->getCell( 'C' . $row )->getValue(),
                    'publisher' => $sheet->getCell( 'D' . $row )->getValue(),
                    'date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell( 'E' . $row )->getValue()),
                    'edition' => $sheet->getCell( 'F' . $row )->getValue(),
                    'brand' => $sheet->getCell( 'G' . $row )->getValue(),
                    'quantity' => $sheet->getCell( 'H' . $row )->getValue(),
                    'book_code' => $sheet->getCell( 'J' . $row )->getValue(),
                    'mrp' => $sheet->getCell( 'K' . $row )->getValue(),
                    'almari_no' => $sheet->getCell( 'L' . $row )->getValue(),
                    'cover' => $sheet->getCell( 'M' . $row )->getValue(),
                     
                ];
                $startcount++;
            }
            
            DB::table('library_books')->insert($data);
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return redirect('library_book_add')->with('error','Error Books Not Added !');
        }
        return redirect('library_book_view')->with('message','Book Add Successful !');
     }   

        public function libraryBookView(Request $request){
            // dd($request->barcode_download);
            $search['form_date'] = $request->form_date;
        $search['to_date'] = $request->to_date;
        $search['name'] = $request->name;

        $data = LibraryBook::select('library_books.*','libry.name as catname')
            ->LeftJoin ('library_categarys as libry','libry.id','library_books.category_id')
            ->where('library_books.session_id', Session::get('session_id'));
            
        if(Session::get('role_id') > 1){
            $data = $data->where('library_books.branch_id', Session::get('branch_id'));
        }    
     
        if ($request->isMethod('post')) {
            $request->validate([]);
            if (!empty($request->name)) {
                $value = $request->name;
                $data = $data = $data->where(function ($query) use ($value) {
                        $query->where('library_books.name', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.author', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.publisher', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.edition', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.edition', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.brand', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.almari_no', 'like', '%' .$value. '%');
                        $query->orWhere('library_books.book_code', 'like', '%' .$value. '%');
                });
            }
            
            if (!empty($request->form_date)) {
                $data = $data->where("library_books.date", $request->form_date);
            }
            if (!empty($request->to_date)) {
                $data = $data->where("library_books.date", $request->to_date);
            }
        }

        if($request->barcode_download == "barcode_download"){
            $data = $data->orderBy('id', 'DESC')->get();
              $pdf = PDF::loadView('print_file.pdf.barcode',['data'=>$data]);
              return $pdf->download('barcode.pdf');
        }
        
              $data = $data->orderBy('id','DESC')->get();

            return view('library.books.view',['data'=>$data,'search'=>$search]);
        } 
        
        public function libraryBookAdd(Request $request){
           
          
            if($request->isMethod('post')){
              
                $request->validate([
                    'name' => 'required',
                    'author' => 'required',
                    'publisher' => 'required',
                    'date' => 'required',
                    'edition' => 'required',
                    'mrp' => 'required',
                    'quantity' => 'required',
                    'category_id' => 'required',
                    
                ]);
                  $Book_img ='';
                    if($request->file('image')){
                     $image = $request->file('image');
                    $path = $image->getRealPath();      
                    $Book_img =  time().uniqid().$image->getClientOriginalName();
                    $destinationPath = env('IMAGE_UPLOAD_PATH').'Book_img';
                    $image->move($destinationPath, $Book_img);  
                    
                 }
                 
                $book =  new LibraryBook;//modal name
                $book->user_id = Session::get('id');
                $book->branch_id = Session::get('branch_id');
                $book->session_id = Session::get('session_id');
                $book->category_id = $request->category_id;
                $book->name = $request->name;
                $book->author = $request->author;
                $book->publisher = $request->publisher;
                $book->date = $request->date;
                $book->edition = $request->edition;
                $book->brand = $request->brand;
                $book->quantity = $request->quantity;
               
                if(!empty($request->book_code)){
                  $book->book_code =  $request->book_code;
                }
                else { 
               $book->book_code = mt_rand(1000,1999);
               } 
                $book->cover = $request->cover;
                $book->barcode_no = $request->barcode_no;
                $book->almari_no = $request->almari_no;
                $book->mrp = $request->mrp;
                $book->image =$Book_img;
                $book->save();   
                return redirect('library_book_view')->with('message','Book Add Successful !');
            } 
    
            return view('library.books.add');
        } 
        
        
        
        public function bookCategoryView(Request $request){
            $data = LibraryCategory::where('session_id',Session::get('session_id'));
            
            if(Session::get('role_id') > 1){
                $data = $data->where('branch_id',Session::get('branch_id'));
            }
            
            $data = $data->orderBy('id','DESC')->get();
            return view('library.category.view',['data'=>$data]);
        } 

        
        public function bookCategoryAdd(Request $request){
            
                   if($request->isMethod('post')){
                
                $request->validate([
                    'name' => 'required',
                   
                ]);
                
                $book =  new LibraryCategory;//modal name
                $book->user_id = Session::get('id');
                $book->branch_id = Session::get('branch_id');
                $book->session_id  = Session::get('session_id');
                $book->name = $request->name;
               
                $book->save();   
                return redirect('book_category_view')->with('message','Book Add Successfully !');
            } 
            return view('library.category.add');
             
           } 
        
       public function bookCategoryEdit(Request $request,$id){
          $book = LibraryCategory::find($id);
          if($request->isMethod('post')){
               $request->validate([
                    'name' => 'required',

                   
                   
                ]);
                
              $book->user_id = Session::get('id');
              $book->branch_id = Session::get('branch_id');
              $book->session_id = Session::get('session_id');
              $book->name = $request->name;
               
                $book->save(); 
                
              return redirect('book_category_view')->with('message', 'Book Edit Successfully !'); 
          }
          
           return view('library.category.edit',['book'=>$book]);
             
        }
        
        
       public function bookCategoryDelete(Request $request){  
           
           $delete = LibraryCategory::where('id',$request->delete_id)->delete();
             return redirect('book_category_view')->with('message', 'Book Category Delete Successfully !');
       }
       
       
       public function libraryBookDelete(Request $request){ 
         
           
           $delete = LibraryBook::where('id',$request->delete_id)->first();
        
           if(!empty($delete->image)){
                 if (File::exists(env('IMAGE_UPLOAD_PATH') . 'Book_img/' . $delete->image)) {
        File::delete(env('IMAGE_UPLOAD_PATH') . 'Book_img/' . $delete->image);
        }  
           }
       
         $delete->delete();
         
             return redirect('library_book_view')->with('message', 'Book Delete Successfully !');
       }
        
             public function bookLibraryEdit(Request $request,$id){
                  
              $book = LibraryBook::find($id);
              
               if($request->isMethod('post')){
                
                $request->validate([
                    'name' => 'required',
                    'author' => 'required',
                    'publisher' => 'required',
                    'date' => 'required',
                    'edition' => 'required',
                    'mrp' => 'required',
                    'brand' => 'required',
                    'quantity' => 'required',
                    'category_id' => 'required',
                    
                ]);
                     
                   if($request->file('image')){
                     $image = $request->file('image');
                    $path = $image->getRealPath();      
                    $Book_img = $image->getClientOriginalName();
                    $destinationPath = env('IMAGE_UPLOAD_PATH').'Book_img';
                    $image->move($destinationPath, $Book_img);    
                     if (File::exists(env('IMAGE_UPLOAD_PATH') . 'Book_img/' . $book->image)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'Book_img/' . $book->image);
                    }
                    $book->image = $Book_img;
                 }
                  
                  $book->user_id = Session::get('id');
                  $book->branch_id = Session::get('branch_id');
                  $book->session_id = Session::get('session_id');
                  $book->name = $request->name;
                  $book->author = $request->author;
                  $book->publisher = $request->publisher;
                  $book->date = $request->date;
                  $book->edition = $request->edition;
                  $book->brand = $request->brand;
                  $book->mrp = $request->mrp;
                  $book->almari_no = $request->almari_no;
                  
                    if(!empty($request->book_code)){
                  $book->book_code =  $request->book_code;
                }
                else { 
               $book->book_code = mt_rand(1000,1999);
               } 
                  $book->quantity = $request->quantity;
                  $book->cover = $request->cover;
                  $book->category_id = $request->category_id;
                  $book->save(); 
                    
                  return redirect('library_book_view')->with('message', 'Book Edit Successfully !'); 
              }
              
               return view('library.books.edit',['book'=>$book]);
                 
            }
        


}