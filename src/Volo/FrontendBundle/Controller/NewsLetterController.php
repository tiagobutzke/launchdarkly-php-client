<?php

namespace Volo\FrontendBundle\Controller;

use Composer\Json\JsonValidationException;
use Foodpanda\ApiSdk\Exception\ApiException;
use Foodpanda\ApiSdk\Exception\ValidationEntityException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsLetterController extends BaseController
{
    /**
     * @Route(
     *      "/newsletter/subscribe",
     *      name="newsletter.subscribe",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function subscribeAction(Request $request)
    {
        $newsletter = $this->decodeJsonContent($request->getContent());

        try {
            $this->get('volo_frontend.provider.newsletter')->subscribe(
                $newsletter['email'],
                $newsletter['city_id']
            );
        } catch (ValidationEntityException $e) {
            return new JsonResponse([
                'error' => [
                    'errors' => json_decode($e->getJsonErrorMessage(), true)
                ]
            ], Response::HTTP_BAD_REQUEST);
        } catch (ApiException $e) {
            // Here as per the api the only error you get in this case is related to email.
            return new JsonResponse(
                [
                    'error' => [
                        'errors' => [
                            [
                                'field_name' => 'email',
                                'violation_messages' => [
                                    $this->get('translator')->trans($e->getMessage()),
                                ],
                            ]
                        ]
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    /**
     * @Route(
     *      "/newsletter/unsubscribe/code/{code}",
     *      name="newsletter.unsubscribe_by_code",
     *      options={"expose"=true},
     *      requirements={
     *          "code": "([A-Za-z0-9]+)"
     *      }
     * )
     * @Method({"GET"})
     * @param string $code
     *
     * @return RedirectResponse
     */
    public function unsubscribeAction($code)
    {
        try {
            $this->get('volo_frontend.provider.newsletter')->unsubscribeByCode($code);
            $isUnsubscribed = true;
        } catch (ApiException $e) {
            $isUnsubscribed = false;
        }

        return $this->redirectToRoute('home', ['showUnsubscribePopup' => true, 'isUnsubscribed' => $isUnsubscribed]);
    }
}
