<?php
require_once 'Controller.php';
require_once 'models/Department.php';
require_once 'models/User.php';
require_once 'models/Course.php';
require_once 'models/Term_Offering.php';
class CoursesController extends Controller
{

    public static function index($method)
    {
        if (isset($_GET['edit'])) {
            CoursesController::update($method);
        } elseif (isset($_GET['delete'])) {
            CoursesController::delete($method);
        } elseif (isset($_GET['create'])) {
            CoursesController::create($method);
        } else {
            $user = User::findBy(['username' => 'admin']); // development data
            // after login works
            // $user = User::findBy(['username' => $_SESSION['username']]);

            $active_term_courses = TermOffering::courses();
            $active_courses = [];
            foreach ($active_term_courses as $course) {
                $active_courses[] = $course->values['course_id'];
            }
            $departments = Department::all();
            require_once 'views/courses.php';
        }
    }

    public static function get_courses()
    {
        $courses = Course::all();
        return $courses;
    }

    public static function update($method)
    {

        if ($method == "POST") {
            $course_id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);
            $name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
            $credits = filter_input(INPUT_POST, 'credits', FILTER_VALIDATE_INT);
            $desc = filter_input(INPUT_POST, 'desc', FILTER_DEFAULT);
            $course = Course::find($course_id);

            if (empty($name) || empty($credits) || empty($desc || empty($course_id))) {
                $_SESSION['error'] = 'All fields are required';
                redirect_back();
            }

            if ($credits < 1) {
                $_SESSION['error'] = 'Credits must be higher or equal than 1';
                redirect_back();
            }

            try {
                $description_bbcode = parseBBCode($desc, ['unkeyed', 'Prerequisites']);
            } catch (MissingKeysException $e) {
                $_SESSION['error'] = 'Missing: ' . implode(', ', $e->missing_keys);
                redirect_back();
            }



            $failed = $course->updatePrerequisites($description_bbcode['Prerequisites']);

            if (sizeof($failed) > 0) {
                $_SESSION['error'] = 'Failed to add prerequisite(s) ' .
                    implode(', ', $failed) .
                    ' must be 4 uppercase letters followed by 4 numbers';
                redirect_back();
            }

            // this will update and save the course new information
            $success = $course->update([
                'course_name' => $name,
                'course_credits' => $credits,
                'course_desc' => $description_bbcode['unkeyed'],
                'updated_by' => Auth::user()->username
            ]);

            $success ?
                $_SESSION['success'] = 'Changed course information successfully' :
                $_SESSION['error'] = 'Failed to change course information';

            redirect('?courses');
        } else {
            $course_id = $_GET['edit'];
            try {
                $course = Course::find($course_id);
            } catch (ModelNotFoundException $e) {
                $_SESSION['error'] = 'The course does not exist';
                redirect('?courses');
            }

            if (!Auth::checkAdmin() && Auth::user()->dept_id !== $course->dept_id) {
                $_SESSION['error'] = 'You don\'t have permissions to edit this course.';
                redirect('?courses');
            }

            require_once 'views/course_edit.php';
        }
    }

    public static function create($method)
    {
        if (!Auth::check()) {
            $_SESSION['error'] = 'View not found';
            redirect('index.php?courses');
        }

        if ($method === "POST") {
            $name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
            $code = filter_input(INPUT_POST, 'code', FILTER_DEFAULT); // this must be unique
            $credits = filter_input(INPUT_POST, 'credits', FILTER_VALIDATE_INT);
            $description = filter_input(INPUT_POST, 'description', FILTER_DEFAULT);
            $department = filter_input(INPUT_POST, 'department', FILTER_DEFAULT);

            // validate here the specific inputs

            // validating for required fields
            if (empty($name) || empty($code) || empty($credits) || empty($description) || empty($department)) {
                $_SESSION['error'] = 'All fields are required';
                redirect_back();
            }

            if (Course::exists(['course_id' => $code])) {
                $_SESSION['error'] = 'The course already exists';
                redirect_back();
            }

            if ($credits < 1) {
                $_SESSION['error'] = 'Credits must be higher or equal than 1';
                redirect_back();
            }

            // validating for department code
            if (!is_valid_course_code($code)) {
                $_SESSION['error'] = 'The course code must be 4 uppercase letters followed by 4 digits';
                redirect_back();
            }

            if (!Auth::checkAdmin() && Auth::user()->dept_id !== $department) {
                $_SESSION['error'] = 'You are not allowed to create a course in this department';
                redirect_back();
            }

            try {
                $department = Department::find($department);
            } catch (ModelNotFoundException $e) {
                $_SESSION['error'] = 'The department does not exist';
                redirect_back();
            }

            // reading the description as BBC
            try {
                $bbc_description = parseBBCode($description, ['unkeyed']);
            } catch (MissingKeysException $e) {
                $_SESSION['error'] = 'Missing keys: ' . implode(', ', $e->missing_keys);
                redirect_back();
            }



            // Creating the course

            try {
                $newCourse = Course::create([
                    'course_name' => $name,
                    'course_id' => $code,
                    'course_credits' => $credits,
                    'course_desc' => $bbc_description['unkeyed'],
                    'dept_id' => $department->dept_id,
                    'updated_by' => Auth::user()->username,
                ]);
            } catch (PDOException $e) {
                $_SESSION['error'] = "Please enter unique values";
                redirect_back();
            }


            // Adding the prerequisite

            if (in_array('Prerequisites', array_keys($bbc_description))) {
                $prerequisites = $bbc_description['Prerequisites'];
                foreach ($prerequisites as $prerequisite) {
                    $newCourse->addPrerequisite($prerequisite);
                }
            }

            $_SESSION['success'] = 'Course created successfully';
            redirect('?courses');
        } else {

            // check if the user can create the course of the department
            if (empty($_GET['create']) && !Auth::checkAdmin()) {
                $_SESSION['error'] = 'Specify a department to create a course';
                redirect('?courses');
            }

            if (!Auth::checkAdmin() && Auth::user()->dept_id !== $_GET['create']) {
                $_SESSION['error'] = 'You don\'t have permissions to create a course of this department';
                redirect('?courses');
            }
            $departments = Department::all();
            require_once 'views/course_create.php';
        }
    }

    public static function delete($method)
    {
        $course = $_GET['delete'];
        try {
            $course = Course::find($course);
        } catch (ModelNotFoundException $e) {
            $_SESSION['error'] = 'The course does not exist';
            redirect('?courses');
        }
        $result = TermOffering::delete_course($course->values['course_id']);
        require_once 'views/course_delete.php';
    }
}
