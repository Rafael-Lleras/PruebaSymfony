<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CategoryController extends AbstractController {

    /**
     * @Route("/categories/{order_by}", name="categories")
     */
    public function index($order_by = 'name') {
    	$repository = $this->getDoctrine()->getRepository(Category::class);
		$categories = $repository->getAllCategories($order_by);
		return $this->render(
            'category/index.html.twig', 
            array(
                'data' => $categories, 
                'order_by' => $order_by
            )
    	);
    }

    /**
     * @Route("/change_state_of_category/{id}", name="change_state_of_category")
     */
    public function change_state_of_category($id) {
    	$entityManager = $this->getDoctrine()->getManager();
	    $category = $entityManager->getRepository(Category::class)->find($id);
	    if (!$category) {
	        throw $this->createNotFoundException(
	            'No existe una categoría con el id: ' . $id
	        );
	    }
	    $old_state = $category->getActive();
	    $new_state = ($old_state + 1) % 2;
	    $category->setActive($new_state);
	    $entityManager->flush();

	    return $this->redirectToRoute('categories', []);
    }

    /**
     * @Route("/category/update/{id}", name="update_category")
     */
    public function update(Category $category, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Categoría editada correctamente');
            return $this->redirectToRoute('categories', [
                'id' => $category->getId()
            ]);
        }
        return $this->render('category/update.html.twig', [
            'category_form' => $form->createView()
        ]);
    }

     /**
     * @Route("/category/insert", name="insert_category")
     */
    public function insert(Category $category = null, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setActive(true);

            $name = $category->getName();
            $code = $category->getCode();
            $equals = $this->nameAndCodeAreUniques($name, $code);
            $are_there_equals = count($equals) > 0  ;
            if(!$are_there_equals) {
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', 'Categoría insertada correctamente');
                return $this->redirectToRoute('categories', [
                    'id' => $category->getId()
                ]);
            } else {
                $this->addFlash('error', 'Ya existen una categoría con el mismo nombre o código.');
                return $this->render('category/insert.html.twig', [
                    'category_form' => $form->createView()
                ]);
            }
        }
        return $this->render('category/insert.html.twig', [
            'category_form' => $form->createView()
        ]);
    }

    private function nameAndCodeAreUniques($name, $code) {

        $repository = $this->getDoctrine()->getRepository(Category::class);
        $data = $repository->nameAndCodeAreUniques($name, $code);
        return $data;
    }

}
