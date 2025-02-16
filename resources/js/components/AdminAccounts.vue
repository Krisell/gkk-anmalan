<template>
  <div class="container mx-auto">
    <div v-if="ungranted.length > 0" class="flex flex-col mb-8">
      <h2 class="text-2xl font-thin text-center m-4">Väntar på godkännande</h2>
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Datum reg.
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in ungranted" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }}
                      </div>
                      <div class="text-sm leading-5 text-gray-500">{{ account.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>

                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <Button @click="grantAccount(account)">Godkänn konto</Button>
                  <Button class="ml-2" type="secondary" @click="deleteAccount(account)">Radera konto</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <h2 class="text-2xl font-thin text-center m-4">
      Lista med {{ sortedActiveAccounts.length }} aktiva konton
      <i style="margin-left: 20px; cursor: pointer"
        v-tooltip="'Klicka för att kopiera epostadresserna för alla ej inaktiverade konton'" @click="copyEmails"
        class="fa fa-envelope"></i>
    </h2>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <div class="flex flex-col items-center">
            <div class="relative my-4 ml-4 flex gap-6 text-sm font-thin">
              <div class="flex flex-col gap-2">
                Kassörsläge
                <ToggleButton v-model="treasurerMode"  />
              </div>
              <div class="flex flex-col gap-2">
                Visa ungdomar/juniorer upp till 23
                <ToggleButton v-model="show.juniors"  />
              </div>
              <div class="flex flex-col gap-2">
                Visa studenter över 23
                <ToggleButton v-model="show.studentsOver23"  />
              </div>
              <div class="flex flex-col gap-2">
                Visa övriga
                <ToggleButton v-model="show.rest"  />
              </div>
            </div>
            <div class="relative my-2 rounded-md shadow-sm w-64 ml-4 lg:mx-auto">
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fa fa-search text-gray-400"></i>
              </div>
              <div 
                v-if="search.length > 0"
                @click="search = ''" 
                class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3"
              >
                <i class="fa fa-times text-gray-400"></i>
              </div>
              <input 
                v-model="search" 
                class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" placeholder="Sök medlem" 
              />
            </div>
          </div>
          <table class="min-w-full">
            <thead>
              <tr>
                <th @click="sortBy('first_name')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Namn
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  
                </th>
                <th
                @click="sortBy('event_registrations')"
                  v-show="!treasurerMode"
                  class="cursor-pointer px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
                  v-tooltip="'Bekräftad närvaro som funktionär. Inom parentes senaste 365 dagarna'"
                >
                  Bekr. funk.<br />(senaste 365 dagarna)
                </th>
                <th @click="sortBy('created_at')"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Regdatum
                </th>
                <th @click="sortBy('last_visited_at')"
                  class="w-[130px] px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Senaste besök
                </th>
                <th @click="sortBy('visits')"
                  v-show="!treasurerMode"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Antal besök
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Licens.avg. 2025
                </th>
                <!-- <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() + 1 }}
                </th> -->
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() }}
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Medl.avg. {{ getCurrentYear() - 1 }}
                </th>
                <th v-show="!treasurerMode" class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in filteredSortedActiveAccounts" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }} 
                        <i
                        v-tooltip="{content: `${account.email}<br>Klicka för att kopiera.`, html: true}" @click="copyEmail(account.email)"
                        class="fa fa-envelope-o ml-2 cursor-pointer"></i>
                      </div>
                      <span v-if="account.licence_number" class="text-xs font-light">
                        {{ account.licence_number }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <i v-if="isJunior(account)" class="fa fa-child text-gkk text-xl" v-tooltip="'Ungdom / Junior'"></i>
                  <i v-else-if="account.is_student_over_23" class="fa fa-graduation-cap text-gkk text-xl" v-tooltip="'Student över 23'"></i>
                  <div v-if="isLOKAllowed(account)" class="text-gkk text-xl" v-tooltip="'LOK-berättigad'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:2"><path d="m37.278 14.519-21.203-2.45a4 4 0 0 0-4.433 3.514l-.763 6.606" style="fill:none;stroke:#222a33;stroke-width:2px"/><path d="m15.001 20.404.322-2.782a1.867 1.867 0 0 1 2.069-1.641l2.976.344M53.202 19.117a4.001 4.001 0 0 0-4.609-3.28l-38.149 6.425a4.001 4.001 0 0 0-3.28 4.609l3.634 21.575a4 4 0 0 0 4.609 3.28l38.149-6.425a4.003 4.003 0 0 0 3.28-4.609l-3.634-21.575z" style="fill:none;stroke:#222a33;stroke-width:2px"/><path d="m19.474 47.253-2.823.476a1.999 1.999 0 0 1-2.305-1.64l-.443-2.632m30.623-23.148 2.823-.475a1.998 1.998 0 0 1 2.305 1.64l.443 2.631m-38.365 6.462-.443-2.632a1.999 1.999 0 0 1 1.64-2.304l2.823-.476M52.268 36.996l.443 2.631a1.999 1.999 0 0 1-1.64 2.305l-2.823.475M33.221 29.788A2.334 2.334 0 1 0 32 33.781a2.336 2.336 0 0 1 2.69 1.914 2.336 2.336 0 0 1-3.911 2.079M31.225 29.177l-.311-1.841M33.086 40.226l-.311-1.841" style="fill:none;stroke:#222a33;stroke-width:2px"/></svg>
                  </div>
                  <i v-if="account.is_honorary_member" class="fa fa-trophy text-gkk text-xl" v-tooltip="'Hedersmedlem'"></i>
                </td>
                <td v-show="!treasurerMode" class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ presentTotal(account.event_registrations) }}<br />({{
                      presentLastYear(account.event_registrations)
                    }})
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">
                    {{ dateString(account.last_visited_at) }}
                  </div>
                </td>
                <td
                  v-show="!treasurerMode"
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  {{ account.visits }}
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <ToggleButton 
                    v-if="showPaymentToggle(account, 2025, 'SSFLICENSE')" 
                    @update:modelValue="updatePayment(account, 2025, 'SSFLICENSE')"
                    :modelValue="hasPaid(account, 2025, 'SSFLICENSE')" 
                  />
                </td>
                <!-- <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear() + 1, 'MEMBERSHIP')" 
                    @update:modelValue="$event => updatePayment(account, getCurrentYear() + 1, 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear() + 1, 'MEMBERSHIP')" 
                  />
                </td> -->
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear(), 'MEMBERSHIP')" 
                    @update:modelValue="updatePayment(account, getCurrentYear(), 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear(), 'MEMBERSHIP')" 
                  />
                </td>
                <td
                  class="px-6 py-2 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500 text-center">
                  <span v-if="account.is_honorary_member" class="text-xs italic">Hedersmedlem</span>
                  <ToggleButton 
                    v-else-if="showPaymentToggle(account, getCurrentYear() - 1, 'MEMBERSHIP')" 
                    @update:modelValue="updatePayment(account, getCurrentYear() - 1, 'MEMBERSHIP')"
                    :modelValue="hasPaid(account, getCurrentYear() - 1, 'MEMBERSHIP')" 
                  />
                </td>
                <td
                  v-show="!treasurerMode"
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div class="flex items-center gap-2">
                    <div v-if="user.role === 'superadmin'">
                      <i @click="confirmDemotion(account)" v-if="account.role === 'admin'" style="cursor: pointer"
                        class="fa fa-star" v-tooltip="'Administratör, klicka för att ta bort rollen'"></i>
                      <i v-else-if="account.role === 'superadmin'" class="fa fa-star"
                        v-tooltip="'Superadministratör'"></i>
                      <i @click="confirmPromotion(account)" v-else class="fa fa-star-o" style="cursor: pointer"
                        v-tooltip="'Gör till administratör'"></i>
                    </div>
                    <div v-else>
                      <i v-if="account.role === 'admin'" class="fa fa-star" v-tooltip="'Administratör'"></i>
                      <i v-if="account.role === 'superadmin'" class="fa fa-star" v-tooltip="'Superadministratör'"></i>
                    </div>
                    <i @click="confirmInactivation(account)" class="fa fa-ban cursor-pointer"
                      v-tooltip="'Inaktivera konto – personen kommer inte kunna logga in förrän kontot återaktiveras.'">
                    </i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

        </div>
        <div class="text-center mt-4" v-tooltip="'Excel-fil med medlemmarna som visas ovan'">
          <svg
            @click="excel"
            class="mx-auto cursor-pointer"
            xmlns="http://www.w3.org/2000/svg"
            x="0px"
            y="0px"
            width="60"
            height="60"
            viewBox="0 0 100 100"
            style="fill: #000000"
          >
            <path
              fill="#a0d2a1"
              d="M69.307,81.575h-39c-6.6,0-12-5.4-12-12v-39c0-6.6,5.4-12,12-12h39c6.6,0,12,5.4,12,12v39C81.307,76.175,75.907,81.575,69.307,81.575z"
            ></path>
            <path
              fill="#1f212b"
              d="M69.307,82.575h-39c-7.18,0-13-5.82-13-13v-39c0-7.18,5.82-13,13-13h39c7.18,0,13,5.82,13,13v39C82.307,76.755,76.487,82.575,69.307,82.575z M19.307,30.575v39c0,6.075,4.925,11,11,11h39c6.075,0,11-4.925,11-11v-39c0-6.075-4.925-11-11-11h-39C24.232,19.575,19.307,24.5,19.307,30.575z"
            ></path>
            <path
              fill="#fdfcee"
              d="M67.186,76.575H33.428c-5.566,0-10.121-4.554-10.121-10.121V32.696c0-5.566,4.554-10.121,10.121-10.121h33.759c5.566,0,10.121,4.554,10.121,10.121v33.759C77.307,72.02,72.753,76.575,67.186,76.575z"
            ></path>
            <path
              fill="#1f212b"
              d="M66.47 77.575H33.144c-5.985 0-10.837-4.852-10.837-10.837V33.413c0-5.986 4.852-10.838 10.838-10.838h33.662c.276 0 .5.224.5.5s-.224.5-.5.5H33.144c-5.433 0-9.837 4.404-9.837 9.837v33.326c0 5.433 4.404 9.837 9.837 9.837H66.47c5.433 0 9.837-4.404 9.837-9.837V48.075c0-.276.224-.5.5-.5s.5.224.5.5v18.663C77.307 72.723 72.455 77.575 66.47 77.575zM76.807 45.5c-.276 0-.5-.224-.5-.5v-4c0-.276.224-.5.5-.5s.5.224.5.5v4C77.307 45.276 77.083 45.5 76.807 45.5zM76.807 39.5c-.276 0-.5-.224-.5-.5v-2c0-.276.224-.5.5-.5s.5.224.5.5v2C77.307 39.276 77.083 39.5 76.807 39.5z"
            ></path>
            <path
              fill="#a0d2a1"
              d="M62.929 61.878L55.331 61.878 50.148 54.548 44.702 61.878 37.071 61.878 46.539 49.971 38.894 39.558 46.602 39.558 50.183 45.22 53.998 39.558 61.842 39.558 53.827 49.971 62.929 61.878"
            ></path>
            <path
              fill="#1f212b"
              d="M62.929,62.379h-7.598c-0.162,0-0.314-0.079-0.408-0.211l-4.785-6.769l-5.035,6.777c-0.094,0.127-0.243,0.202-0.401,0.202h-7.631c-0.192,0-0.367-0.11-0.45-0.283s-0.061-0.378,0.059-0.528l9.23-11.607l-7.418-10.106c-0.112-0.152-0.128-0.354-0.043-0.521c0.085-0.168,0.258-0.274,0.446-0.274h7.708c0.171,0,0.331,0.088,0.422,0.232l3.172,5.017l3.388-5.028c0.093-0.138,0.248-0.221,0.415-0.221h7.843c0.19,0,0.364,0.108,0.449,0.279c0.084,0.171,0.063,0.375-0.052,0.525l-7.781,10.11l8.869,11.603c0.115,0.151,0.135,0.354,0.051,0.525C63.293,62.27,63.12,62.379,62.929,62.379z M55.59,61.379h6.328L53.43,50.275c-0.137-0.18-0.137-0.43,0.001-0.608l7.395-9.609h-6.562L50.597,45.5c-0.094,0.141-0.252,0.235-0.422,0.221c-0.169-0.003-0.325-0.09-0.415-0.232l-3.434-5.431h-6.445l7.06,9.618c0.134,0.182,0.129,0.431-0.011,0.607l-8.823,11.096h6.344l5.296-7.129c0.095-0.129,0.246-0.204,0.407-0.202c0.16,0.002,0.31,0.08,0.402,0.211L55.59,61.379z"
            ></path>
          </svg>
        </div>
      </div>
    </div>

    <div class="flex flex-col mt-4 items-center justify-center">
      <h2 class="text-xl font-thin text-center m-4">Lägg till nya konton</h2>
      <div class="w-1/2 font-thin mb-4">
        Nedan kan du klistra in konton från exempelvis Excel, eller skriva manuellt. Ange dessa i formatet "Förnamn
        Efternamn Epost" där antingen tabb eller ; (semikolon) avgränsar fälten. Ange en person per rad.
      </div>
      <div class="w-1/2 font-thin mb-4">
        Epostadresser som redan finns i systemet kommer ignoreras. Tillagda konton kommer automatiskt skicka ut ett
        epost till användaren med en länk för att logga in och ange ett lösenord.
      </div>
      <textarea v-model="newAccountsString" class="mx-auto w-1/2 h-32 border rounded p-2"></textarea>
      <Button @click="createAccounts" class="mt-2">Skapa konton</Button>
    </div>

    <div v-if="inactiveAccounts.length > 0" class="flex flex-col mb-8 mt-8">
      <h2 class="text-2xl font-thin text-center m-4">{{ inactiveAccounts.length }} inaktiverade konton</h2>
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Namn
                </th>
                <th
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Registreringsdatum
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="account in inactiveAccounts" :key="account.id">
                <td class="px-2 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ account.first_name }} {{ account.last_name }} 
                        <i
                        v-tooltip="`${account.email}<br>Klicka för att kopiera.`" @click="copyEmail(account.email)"
                        class="fa fa-envelope-o ml-2 cursor-pointer"></i>
                      </div>
                      <span v-if="account.licence_number" class="text-xs font-light">
                        {{ account.licence_number }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 text-gray-500 text-center">{{ dateString(account.created_at) }}</div>
                </td>

                <td
                  class="px-6 py-2 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <Button @click="reactivate(account)">Återaktivera konto</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Modal ref="promoteModal" :title="`Är du säker på att du vill göra ${selectedAccount && selectedAccount.email} till administratör?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="promote" type="danger">Ja, gör till administratör</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="demoteModal" :title="`Är du säker på att du vill ta bort administratörsrollen för ${selectedAccount && selectedAccount.email}?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="demote" type="danger">Ja, ta bort</Button>
        </div>
      </template>
    </Modal>

    <Modal ref="inactivateModal" :title="`Är du säker på att du vill inaktiverat kontot för ${selectedAccount && selectedAccount.email}?`">
      <template #footer="{ close }">
        <div class="flex gap-2 items-center justify-center mt-4">
          <Button @click="close" type="secondary">Nej</Button>
          <Button @click="inactivate" type="danger">Ja, inaktivera</Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'
import Modal from './ui/Modal.vue'
import Button from './ui/Button.vue'
import Date from '../modules/Date.js'

export default {
  components: { Button, ToggleButton, Modal },
  props: {
    initialAccounts: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    ungranted: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedAccount: null,
      grantStatus: '',
      sortKey: 'visits',
      sortOrder: -1,
      newAccountsString: '',
      accounts: this.initialAccounts,
      search: '',
      treasurerMode: false,
      show: {
        juniors: true,
        studentsOver23: true,
        rest: true,
      }
    }
  },
  async mounted() {
    const url = new URL(window.location.href)
    const sort = url.searchParams.get('sort')
    const order = url.searchParams.get('order')

    if (sort) {
      this.sortKey = sort
    }

    if (order) {
      this.sortOrder = parseInt(order)
    }
  },
  computed: {
    filteredSortedActiveAccounts() {
      let accounts = this.sortedActiveAccounts

      if (!this.show.juniors) {
        accounts = accounts.filter((account) => !this.isJunior(account))
      }

      if (!this.show.studentsOver23) {
        accounts = accounts.filter((account) => !account.is_student_over_23)
      }

      if (!this.show.rest) {
        accounts = accounts.filter((account) => this.isJunior(account) || account.is_student_over_23)
      }

      if (this.search === '') {
        return accounts
      }

      // Fuzzy search on full name and email string
      return accounts.filter((account) => {
        const search = this.search.toLowerCase()
        const fullName = `${account.first_name} ${account.last_name}`.toLowerCase()
        const email = account.email.toLowerCase()

        return fullName.includes(search) || email.includes(search)
      })
    },
    sortedActiveAccounts() {
      return this.accounts
        .filter((account) => account.inactivated_at === null)
        .sort((a, b) => {
          if (this.sortKey === 'event_registrations') {
            return this.sortOrder * (this.presentLastYear(b.event_registrations) - this.presentLastYear(a.event_registrations))
          }

          return this.sortOrder *
            String(a[this.sortKey]).localeCompare(String(b[this.sortKey]), undefined, { numeric: true })
        }
        )
    },
    inactiveAccounts() {
      return this.accounts.filter((account) => account.inactivated_at !== null)
    },
  },
  methods: {
    async loadAccounts() {
      const response = await axios.get('/admin/accounts')
      this.accounts = response.data
    },
    dateString(date) {
      if (!date) {
        return ''
      }

      return date.substr(0, 10)
    },
    copyEmail(email) {
      navigator.clipboard.writeText(email)
      this.$toast.success(`Epostadress kopierad: ${email}`)
    },
    copyEmails() {
      navigator.clipboard.writeText(
        this.accounts
          .filter((account) => account.inactivated_at === null)
          .map((account) => account.email)
          .sort((a, b) => a.localeCompare(b))
          .join('; '),

        this.$toast.success('Epostadresser kopierade')
      )
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder *= -1
      }

      this.sortKey = key

      // Update query string to reflect sort order
      const url = new URL(window.location.href)
      url.searchParams.set('sort', this.sortKey)
      url.searchParams.set('order', this.sortOrder)
      window.history.replaceState({}, '', url)
    },
    grantAccount(account) {
      if (this.grantStatus !== '') {
        return
      }

      this.grantStatus = 'pending'
      axios.post(`/admin/accounts/${account.id}/grant`).then(() => {
        window.location.reload()
      })
    },
    deleteAccount(account) {
      if (this.grantStatus !== '') {
        return
      }

      this.grantStatus = 'pending'
      axios.delete(`/admin/accounts/${account.id}/grant`).then(() => {
        window.location.reload()
      })
    },
    getCurrentYear() {
      return new window.Date().getFullYear()
    },
    presentTotal(registrations) {
      return registrations.filter((registration) => registration.presence_confirmed).length
    },
    presentLastYear(registrations) {
      return registrations.filter(
        (registration) => registration.presence_confirmed && Date.withinAYear(registration.event.date),
      ).length
    },
    confirmDemotion(account) {
      this.selectedAccount = account
      this.$refs.demoteModal.show()
    },
    demote() {
      axios.post(`/admin/accounts/demote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    confirmPromotion(account) {
      this.selectedAccount = account
      this.$refs.promoteModal.show()
    },
    confirmInactivation(account) {
      this.selectedAccount = account
      this.$refs.inactivateModal.show()
    },
    promote() {
      axios.post(`/admin/accounts/promote/${this.selectedAccount.id}`).then(() => this.reload())
    },
    inactivate() {
      axios.post(`/admin/accounts/inactivate/${this.selectedAccount.id}`).then(() => this.reload())
    },
    reactivate(account) {
      axios.post(`/admin/accounts/reactivate/${account.id}`).then(() => this.reload())
    },
    async createAccounts() {
      const lines = this.newAccountsString.split(/\n+/).filter((line) => line.length > 0)

      const accounts = lines.map((line) => {
        const [firstName, lastName, email] = line.split(/[\t;]+/)
        return { firstName, lastName, email }
      })

      const response = await axios.post('/admin/accounts', { accounts })

      console.log(response)
    },
    hasPaid(user, year, paymentType) {
      return user.payments.some(payment => payment.type === paymentType && payment.year === year && payment.state === 'PAID')
    },
    showPaymentToggle(user, year, paymentType) {
      return user.payments.some(payment => payment.type === paymentType && payment.year === year)
    },
    async updatePayment(user, year, paymentType) {
      const paymentTypeMessage = paymentType == 'MEMBERSHIP' ? 'Medlemsavgiften' : 'Licens'
      const payment = user.payments.find(payment => payment.type === paymentType && payment.year === year)
      const name = `${user.first_name} ${user.last_name}`

      if (this.hasPaid(user, year, paymentType)) {
        await axios.patch(`/payments/${payment.id}`, { state: null })
        this.$toast.warning(`${paymentTypeMessage} för ${name} har markerats som obetald`)
      } else {
        await axios.patch(`/payments/${payment.id}`, { state: 'PAID' })
        this.$toast.info(`${paymentTypeMessage} för ${name} har markerats som betald`)
      }

      this.loadAccounts()
    },
    isJunior(account) {
      return new window.Date().getFullYear() - account.birth_year <= 23
    },
    isLOKAllowed(account) {
      return new window.Date().getFullYear() - account.birth_year <= 25
    },
    excel() {
      import(/* webpackChunkName: "zipcelx" */ 'zipcelx').then(({ default: zipcelx }) => {
        zipcelx({
          filename: 'gkk-members',
          sheet: {
            data: [
              [
                { value: 'Namn', type: 'string' },
                { value: 'Epost', type: 'string' },
                { value: 'Licensnummer', type: 'string' },
                { value: 'Har betalt medlemsavgift för ' + this.getCurrentYear(), type: 'string' },
                { value: 'Registreringsdatum', type: 'string' },
                { value: 'Senaste besök', type: 'string' },
                { value: 'Antal besök', type: 'string' },
              ],
              ...this.filteredSortedActiveAccounts.map((user) => {
                return [
                  { value: `${user.first_name} ${user.last_name}`, type: 'string' },
                  { value: user.email, type: 'string' },
                  { value: user.licence_number, type: 'string' },
                  { value: user.is_honorary_member ? 'Hedersmedlem' : this.hasPaid(user, this.getCurrentYear(), 'MEMBERSHIP') ? 'Ja' : 'Nej', type: 'string' },
                  { value: this.dateString(user.created_at), type: 'string' },
                  { value: this.dateString(user.last_visited_at), type: 'string' },
                  { value: user.visits, type: 'string' },
                ]
              }),
            ],
          },
        })
      })
    },
  },
}
</script>
