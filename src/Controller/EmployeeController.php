<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Storage\StorageAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @Route("/")
 */
class EmployeeController extends AbstractController
{
    private $storageAdapter;

    public function __construct(StorageAdapter $storageAdapter)
    {
        $this->storageAdapter = $storageAdapter;
    }
    /**
     * @Route("/", name="employee_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $this->storageAdapter->findAll(),
            'chartData' => $this->storageAdapter->getChartData(),
        ]);
    }

    /**
     * @Route("/new", name="employee_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
          
        $form = $this->createForm(EmployeeType::class);
        $form->add('role', ChoiceType::class, ['choices'  => Employee::FORM_CHOICES, 'mapped' => false]);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $entityType = 'App\\Entity\\' . ucfirst($form['role']->getData());
            /** @var Employee $entity */
            $entity = new $entityType;
            $entity->setEmployee(
                $data->getFirstName(),
                $data->getLastName(),
                $data->getGender(),
                $data->getDob(),
                $data->getEmail()
            );
            $this->storageAdapter->storeEntity($entity);
            return $this->redirectToRoute('employee_index');
        }

        return $this->render('employee/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{uuid}", name="employee_show", methods={"GET"})
     */
    public function show($uuid): Response
    {
        $employee = $this->storageAdapter->searchByUuid($uuid);
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="employee_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, string $uuid): Response
    {
        $employee = $this->storageAdapter->searchByUuid($uuid);
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->add('role', ChoiceType::class, ['choices'  => Employee::FORM_CHOICES, 'mapped' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $data = $form->getData();
            
            $entityType = 'App\\Entity\\' . ucfirst($form['role']->getData());
            /** @var Employee $entity */
            $entity = new $entityType;
            $entity->setEmployee(
                $data->getFirstName(),
                $data->getLastName(),
                $data->getGender(),
                $data->getDob(),
                $data->getEmail(),
                $uuid,
            );
            $this->storageAdapter->updateEntity($entity);

            return $this->redirectToRoute('employee_index');
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{uuid}", name="employee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, string $uuid): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uuid, $request->request->get('_token'))) {
            $this->storageAdapter->deleteEntityByUuid($uuid);
        }

        return $this->redirectToRoute('employee_index');
    }

    // public function createEmployeeForm(string $uuid = null)
    // {
    //     $data = null;
    //     if ($uuid) {
    //         $data = $this->storageAdapter->searchByUuid($uuid);
    //     }

    //     return $this->createFormBuilder($data)
    //         ->add('firstName', TextType::class)
    //         ->add('lastName', TextType::class)
    //         ->add('gender', ChoiceType::class, ['choices'  => ['Male' => 'M', 'Female' => 'F'], 'expanded' => true])
    //         ->add('age', NumberType::class, ['constraints' => new GreaterThan(['value' => 15])])
    //         ->add('email', EmailType::class)
    //         ->add('role', ChoiceType::class, ['choices'  => Employee::FORM_CHOICES])
    //         ->getForm();
    // }
}
