<? 
global $big_data, $product;

if(!isset($big_data['block_active']) || !is_array($big_data['block_active']) || !isset($big_data['block_active']['gift']) || $big_data['block_active']['gift'] != 'y')
{ 
     ?><!-- Заказ в подарок START -->
     <div class="order-gift order-gift--tune">
		<div class="similar__title title">
			Заказав этот товар, вы получите в подарок:
		</div>
          <div class="order-gift__inner">
               <div class="order-gift__item">
                    <div class="order-gift__head">
                         <div class="order-gift__head-image order-gift__head-bg1"></div>
                         <div class="order-gift__head-tlt">Бесплатная доставка</div>
						 <div class="order-gift__head-desc">Бесплатно доставим Ваш заказ по городу. Стоимость доставки в отдаленные районы городу уточняйте у наших менеджеров</div>
                    </div>
               </div>
               <div class="order-gift__item">
                    <div class="order-gift__head">
                         <div class="order-gift__head-image order-gift__head-bg2"></div>
                         <div class="order-gift__head-tlt">Крафтовый конверт</div>
 						 <div class="order-gift__head-desc">К заказу бесплатно добавим крафтовый конверт, в который положим открытку, подкормку и инструкцию по уходу за цветами</div>
                   </div>
               </div>
               <div class="order-gift__item">
                    <div class="order-gift__head">
                         <div class="order-gift__head-image order-gift__head-bg3"></div>
                         <div class="order-gift__head-tlt">Записка с Вашим текстом</div>
 						 <div class="order-gift__head-desc">К заказу Вы можете добавить одну бесплатную записку и в комментарии указать текст вашего послания</div>
                   </div>
               </div>
               <div class="order-gift__item">
                    <div class="order-gift__head">
                         <div class="order-gift__head-image order-gift__head-bg4"></div>
                         <div class="order-gift__head-tlt">Подкормка для цветов</div>
 						 <div class="order-gift__head-desc">Бесплатно добавим один пакетик подкормки для цветов, который положим в крафтовый конверт.</div>
                   </div>
               </div>
               <div class="order-gift__item">
                    <div class="order-gift__head">
                         <div class="order-gift__head-image order-gift__head-bg5"></div>
                         <div class="order-gift__head-tlt">Инструкция по уходу</div>
						 <div class="order-gift__head-desc">Вместе с крафтовым конвертом Вы получите инструкцию по уходу за цветами.</div>
                    </div>
               </div>
          </div>
     </div><?
}
?><!-- Заказ в подарок END -->