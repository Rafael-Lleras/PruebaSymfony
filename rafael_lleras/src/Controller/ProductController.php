<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\FilterProductsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ProductController extends AbstractController {

    /**
     * @Route("/products/{order_by}", name="products")
     */
    public function index(
    	$order_by = 'name') {
    	$repository = $this->getDoctrine()->getRepository(Product::class);
		$products = $repository->getAllProducts($order_by);
		return $this->render(
            'product/index.html.twig', 
            array(
                'data' => $products, 
                'order_by' => $order_by
            )
    	);
    }

    /**
     * @Route("/product/filter", name="products_filter")
     */
    public function filter(Product $product = null, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(FilterProductsType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $repository = $this->getDoctrine()->getRepository(Product::class);
        	$data = $repository->filterProducts($product);
            return $this->render('product/filter_results.html.twig', [
                'product_form' => $form->createView(), 
                'data' => $data
            ]);
        }
        return $this->render('product/filter.html.twig', [
            'product_form' => $form->createView()
        ]);
    }    

    /**
     * @Route("/product/delete/{id}", name="delete_product")
     */
    public function delete($id) {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $repository->deleteProduct($id);
        $this->addFlash('success', 'Producto eliminado correctamente');
        return $this->redirectToRoute('products', []);
    }

    /**
     * @Route("/product/update/{id}", name="update_product")
     */
    public function update(Product $product, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Producto editado correctamente');
            return $this->redirectToRoute('products', [
                'id' => $product->getId()
            ]);
        }
        return $this->render('product/update.html.twig', [
            'product_form' => $form->createView()
        ]);
    }

     /**
     * @Route("/product/insert", name="insert_product")
     */
    public function insert(Product $product = null, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(ProductType::class, $product, array());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $name = $product->getName();
            $code = $product->getCode();
            $equals = $this->nameAndCodeAreUniques($name, $code);
            $are_there_equals = count($equals) > 0  ;
            if(!$are_there_equals) {
                $em->persist($product);
                $em->flush();
                $this->addFlash('success', 'Producto insertado correctamente');
                return $this->redirectToRoute('products', [
                    'id' => $product->getId()
                ]);
            } else {
                $this->addFlash('error', 'Ya existen un producto con el mismo nombre o código.');
                return $this->render('product/insert.html.twig', [
                    'product_form' => $form->createView()
                ]);
            }
        }
        return $this->render('product/insert.html.twig', [
            'product_form' => $form->createView()
        ]);
    }

    private function nameAndCodeAreUniques($name, $code) {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $data = $repository->nameAndCodeAreUniques($name, $code);
        return $data;
    }

}
