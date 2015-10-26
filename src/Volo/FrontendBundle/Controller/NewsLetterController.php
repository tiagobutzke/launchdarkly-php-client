<?php

namespace Volo\FrontendBundle\Controller;

use Composer\Json\JsonValidationException;
use Foodpanda\ApiSdk\Exception\ApiException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $newsletter = $request->request->get('newsletter');

        try {
            $this->get('volo_frontend.provider.newsletter')->subscribe(
                $newsletter['email'],
                $newsletter['city_id']
            );
        } catch (ApiException $e) {
            return new JsonResponse(
                [
                    'error' => $this->get('translator')->trans($e->getMessage())
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function unsubscribeAction($code)
    {
        $isUnsubscribed = false;

        try {
            $result = $this->get('volo_frontend.provider.newsletter')->unsubscribeByCode($code);
            $isUnsubscribed = true;
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return $this->redirectToRoute('home', array('unsubscribe' => true, 'isUnsubscribed' => $isUnsubscribed));
    }
}
