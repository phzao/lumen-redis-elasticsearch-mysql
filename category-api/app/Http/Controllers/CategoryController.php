<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * CategoryController constructor.
     *
     * @param CategoryRepositoryInterface $repository
     */
    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $record = $this->repository->all($request->all());

            return $this->respond($record);
        } catch(\PDOException $exception) { //db is offline

            $this->setStatusCode(503);
            return $this->respondWithErrors($exception->getMessage());
        } catch (\Exception $exception) { //error on mysql

            $this->setStatusCode(400);
            return $this->respondWithErrors($exception->getMessage());
        }
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @param          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,
                         Category $category,
                         $id)
    {
        try {
            $request->merge(['id' => $id]);
            $this->validate($request, $category->getRulesID());

            $record = $this->repository->getById($id);

            return $this->respond($record);
        } catch(\PDOException $exception) { //db is offline

            $this->setStatusCode(503);
            return $this->respondWithErrors($exception->getMessage());
        } catch (ValidationException $e) { //object validation is failed

            return $this->respondValidationError($e->errors());
        } catch (\Exception $exception) { //error on mysql

            $this->setStatusCode(400);
            return $this->respondWithErrors($exception->getMessage());
        }
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Category $category)
    {
        try {
            $this->validate($request, $category->rules());

            $record = $this->repository->create($request->all());

            return $this->respondCreated($record);
        } catch(\PDOException $exception) { //db is offline
            $this->setStatusCode(503);

            return $this->respondWithErrors($exception->getMessage());
        } catch (ValidationException $e) { //object validation is failed

            return $this->respondValidationError($e->errors());
        } catch (\Exception $exception) { //error on mysql/redis
            $this->setStatusCode(400);

            return $this->respondWithErrors($exception->getMessage());
        }
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @param          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category, $id)
    {
        try {
            $request->merge(['id' => $id]);
            $this->validate($request, $category->getRulesID());

            $this->repository->update($id, $request->all());

            return $this->respondUpdatedResource();
        } catch(\PDOException $exception) { //db is offline

            $this->setStatusCode(503);
            return $this->respondWithErrors($exception->getMessage());
        } catch (ValidationException $e) { //object validation is failed

            return $this->respondValidationError($e->errors());
        } catch (\Exception $exception) { //error on mysql

            $this->setStatusCode(400);
            return $this->respondWithErrors($exception->getMessage());
        }
    }

    /**
     * @param Request  $request
     * @param Category $category
     * @param          $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, Category $category, $id)
    {
        try {
            $request->merge(['id' => $id]);
            $this->validate($request, $category->getRulesID());

            $this->repository->delete($id);

            return $this->respondUpdatedResource();
        } catch(\PDOException $exception) { //db is offline

            $this->setStatusCode(503);
            return $this->respondWithErrors($exception->getMessage());
        } catch (ValidationException $e) { //object validation is failed

            return $this->respondValidationError($e->errors());
        } catch (\Exception $exception) { //error on mysql

            $this->setStatusCode(400);
            return $this->respondWithErrors($exception->getMessage());
        }
    }
}
